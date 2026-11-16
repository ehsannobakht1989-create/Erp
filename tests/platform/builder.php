<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
require $root.'/builder/src/ProfileParser.php';
require $root.'/builder/src/Resolver.php';
require $root.'/builder/src/Packager.php';

use Builder\Packager;
use Builder\ProfileParser;
use Builder\Resolver;

function builderCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
$remove = function (string $path) use (&$remove): void {
    if (is_dir($path) && ! is_link($path)) {
        foreach (array_diff(scandir($path) ?: [], ['.', '..']) as $entry) {
            $remove($path.'/'.$entry);
        }
        rmdir($path);
    } elseif (file_exists($path) || is_link($path)) {
        unlink($path);
    }
};
$resolver = new Resolver($root);
$manifests = $resolver->manifests();
$resolved = $resolver->resolve(ProfileParser::parseFile($root.'/profiles/minimal.yaml'));
builderCheck(count(array_intersect(['inventory', 'item-master', 'accounting-gl'], $resolved['modules'])) === 3, 'Minimal profile must include its business spine');
$position = array_flip($resolved['modules']);
foreach ($resolved['modules'] as $id) {
    foreach ($manifests[$id]['requires'] ?? [] as $dependency) {
        builderCheck(isset($position[$dependency]) && $position[$dependency] < $position[$id], "Dependency $dependency must be selected before $id");
    }
}
$out = sys_get_temp_dir().'/erp-builder-'.bin2hex(random_bytes(6));
$fake = $out.'-root';
try {
    $result = (new Packager($root, $out, false))->package($resolved);
    $zip = new ZipArchive;
    builderCheck($zip->open($result['zip']) === true && $result['file_count'] === $zip->numFiles && ! $result['vendor'], 'Package must be a readable zip');
    $names = [];
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $names[] = (string) $zip->getNameIndex($i);
    }
    foreach (['artisan', 'public/index.php', 'public/.htaccess', '.htaccess', 'bootstrap/app.php', 'routes/install.php', 'app/Support/Install/Installer.php', 'resources/views/install.blade.php', 'composer.lock', '.env.example', 'storage/logs/.gitignore', 'modules/inventory/module.json', 'modules/inventory/src/Contracts/InventoryServiceInterface.php', 'modules/crm/src/Contracts/CrmServiceInterface.php'] as $expected) {
        builderCheck(in_array($expected, $names, true), "Package must contain $expected");
    }
    builderCheck(array_filter($names, fn (string $name) => preg_match('#^core/|/backend/|^modules/[^/]+/(web|tests)/|^modules/crm/module\.json$|(^|/)\.env$|node_modules/|^vendor/#', $name) === 1) === [], 'Package must exclude legacy kernel, module web/tests, unselected modules, .env and vendor when disabled');
    $config = (string) $zip->getFromName('config/erp-modules.php');
    builderCheck(substr_count($config, "'enabled'") === 1 && str_contains($config, "'inventory'") && ! str_contains($config, "'crm'") && count(array_keys($names, 'config/erp-modules.php', true)) === 1, 'Generated module config must list exactly the selected modules');
    $report = (string) $zip->getFromName('BUILD-REPORT.md');
    builderCheck(str_contains($report, 'WARNING: '.count($resolved['modules']).' selected module(s) have not passed the depth gate') && str_contains($report, 'vendor/ included: NO'), 'Build report must warn about non-done modules and missing vendor');
    $zip->close();
    mkdir($fake.'/modules/solo', 0777, true);
    mkdir($fake.'/vendor', 0777, true);
    file_put_contents($fake.'/modules/solo/module.json', '{"id":"solo","status":"stub","requires":[]}');
    file_put_contents($fake.'/vendor/autoload.php', '<?php');
    $vendor = (new Packager($fake, $out))->package(['factory' => ['slug' => 'solo'], 'modules' => ['solo']]);
    builderCheck($vendor['vendor'] && (new ZipArchive)->open($vendor['zip']) === true, 'Vendor package must be readable');
    $zip = new ZipArchive;
    $zip->open($vendor['zip']);
    builderCheck($zip->locateName('vendor/autoload.php') !== false && str_contains((string) $zip->getFromName('BUILD-REPORT.md'), 'vendor/ included: yes'), 'Existing vendor/ must be packaged');
    $zip->close();
    try {
        (new Packager($fake, $out))->package(['factory' => ['slug' => '../evil'], 'modules' => []]);
        throw new LogicException('Unsafe slug accepted');
    } catch (RuntimeException) {
    }
    echo 'Builder resolver and Laravel packager tests ('.count($resolved['modules']).' modules, '.$result['file_count']." files) passed.\n";
} finally {
    $remove($out);
    $remove($fake);
}
