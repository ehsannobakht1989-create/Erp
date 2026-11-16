<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/app/Support/Modules/ModuleCatalog.php';

use App\Support\Modules\ModuleCatalog;

function check(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
function expectFailure(callable $fn, string $message): void
{
    try {
        $fn();
    } catch (RuntimeException|InvalidArgumentException $e) {
        check(str_contains($e->getMessage(), $message), 'Unexpected error: '.$e->getMessage());
        return;
    }
    throw new LogicException('Expected failure: '.$message);
}
function writeManifest(string $root, string $id, array $requires = [], array $optional = []): void
{
    if (! is_dir("$root/$id")) {
        mkdir("$root/$id", 0777, true);
    }
    file_put_contents("$root/$id/module.json", json_encode(compact('id', 'requires', 'optional'), JSON_THROW_ON_ERROR));
}

$root = sys_get_temp_dir().'/erp-catalog-'.bin2hex(random_bytes(8));
mkdir($root);
try {
    expectFailure(fn () => ModuleCatalog::scan($root), 'No module manifests');
    writeManifest($root, 'base');
    writeManifest($root, 'feature', ['base'], ['optional-missing']);
    writeManifest($root, 'other');
    $catalog = ModuleCatalog::scan($root);
    check($catalog->ordered(['feature', 'base']) === ['base', 'feature'], 'Hard dependencies must come first');
    check($catalog->ordered(['other']) === ['other'], 'Disabled modules must be excluded');
    check($catalog->ordered([]) === [], 'Empty enabled set must remain empty');
    expectFailure(fn () => $catalog->ordered(['feature']), 'requires disabled');
    expectFailure(fn () => $catalog->ordered(['missing']), 'Unknown enabled');
    expectFailure(fn () => $catalog->ordered(['base', 'base']), 'Duplicate enabled');
    writeManifest($root, 'base', ['feature']);
    expectFailure(fn () => ModuleCatalog::scan($root), 'dependency cycle');
    writeManifest($root, 'base', ['missing']);
    expectFailure(fn () => ModuleCatalog::scan($root), 'requires unknown');
    writeManifest($root, 'base', ['other', 'other']);
    expectFailure(fn () => ModuleCatalog::scan($root), 'duplicate');
    file_put_contents("$root/base/module.json", '{');
    expectFailure(fn () => ModuleCatalog::scan($root), 'Invalid JSON');
    echo "Module catalog tests passed.\n";
} finally {
    foreach (glob($root.'/*/module.json') ?: [] as $file) {
        unlink($file);
        rmdir(dirname($file));
    }
    rmdir($root);
}
