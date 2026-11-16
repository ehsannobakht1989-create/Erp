<?php

declare(strict_types=1);

$loader = require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Modules\ModuleCatalog;
use App\Support\Modules\ModuleManager;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

function verifyManager(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
function manifest(string $dir, string $id, array $requires = []): void
{
    mkdir("$dir/$id/Providers", 0777, true);
    file_put_contents("$dir/$id/module.json", json_encode(['id' => $id, 'requires' => $requires, 'optional' => []], JSON_THROW_ON_ERROR));
}
function provider(string $dir, string $id, string $class): void
{
    $source = '<?php namespace Modules\\'.$class.'\\Providers; class ModuleServiceProvider extends \\App\\Support\\Modules\\ModuleServiceProvider { public function register(): void { $this->app->instance("registered.'.$id.'", true); } public function boot(): void { $this->app["router"]->get("/'.$id.'", fn () => "ok")->name("fixture.'.$id.'"); } }';
    file_put_contents("$dir/$id/Providers/ModuleServiceProvider.php", $source);
}
$root = sys_get_temp_dir().'/erp-manager-'.bin2hex(random_bytes(8));
mkdir($root);
try {
    manifest($root, 'fixture-base');
    manifest($root, 'fixture-feature', ['fixture-base']);
    manifest($root, 'fixture-disabled');
    provider($root, 'fixture-base', 'FixtureBase');
    provider($root, 'fixture-feature', 'FixtureFeature');
    provider($root, 'fixture-disabled', 'FixtureDisabled');
    $catalog = ModuleCatalog::scan($root);
    $app = require dirname(__DIR__, 2).'/bootstrap/app.php';
    $app->make(Kernel::class)->bootstrap();
    $manager = new ModuleManager($app, $catalog, $loader, $root);
    $manager->registerEnabled(['fixture-feature', 'fixture-base']);
    verifyManager($app->bound('registered.fixture-base') && $app->bound('registered.fixture-feature'), 'Enabled providers must register');
    verifyManager(! $app->bound('registered.fixture-disabled'), 'Disabled provider must not register');
    $app['router']->getRoutes()->refreshNameLookups();
    verifyManager($app['router']->getRoutes()->getByName('fixture.fixture-base') !== null, 'Enabled module route must register');
    verifyManager($app['router']->getRoutes()->getByName('fixture.fixture-disabled') === null, 'Disabled module route must not register');
    verifyManager(! class_exists('Modules\\FixtureDisabled\\Providers\\ModuleServiceProvider'), 'Disabled provider must not autoload');
    try {
        $manager->registerEnabled([]);
        throw new LogicException('Double registration accepted');
    } catch (RuntimeException $e) {
        verifyManager(str_contains($e->getMessage(), 'already'), 'Double registration must be rejected');
    }
    unlink("$root/fixture-feature/Providers/ModuleServiceProvider.php");
    $app2 = new Application($root);
    try {
        (new ModuleManager($app2, $catalog, $loader, $root))->registerEnabled(['fixture-base', 'fixture-feature']);
        throw new LogicException('Missing provider accepted');
    } catch (RuntimeException $e) {
        verifyManager(str_contains($e->getMessage(), 'no Laravel provider'), 'Missing provider must fail closed');
        verifyManager(! $app2->bound('registered.fixture-base'), 'Preflight must reject before registering providers');
    }
    echo "Module manager tests passed.\n";
} finally {
    foreach (glob($root.'/*/Providers/*') ?: [] as $file) {
        unlink($file);
    }
    foreach (glob($root.'/*') ?: [] as $directory) {
        unlink($directory.'/module.json');
        rmdir($directory.'/Providers');
        rmdir($directory);
    }
    rmdir($root);
}
