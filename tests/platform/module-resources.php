<?php

declare(strict_types=1);

$loader = require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Modules\ModuleCatalog;
use App\Support\Modules\ModuleManager;
use Illuminate\Contracts\Console\Kernel;

function resourceCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$path = sys_get_temp_dir().'/erp-resources-'.bin2hex(random_bytes(8));
mkdir($path);
try {
    foreach (['fixture-resources', 'fixture-off'] as $id) {
        foreach (['Providers', 'routes', 'database/migrations', 'lang'] as $directory) {
            mkdir($path.'/'.$id.'/'.$directory, 0777, true);
        }
        file_put_contents($path.'/'.$id.'/module.json', json_encode(['id' => $id, 'requires' => [], 'optional' => []], JSON_THROW_ON_ERROR));
        $name = implode('', array_map('ucfirst', explode('-', $id)));
        file_put_contents($path.'/'.$id.'/Providers/ModuleServiceProvider.php', '<?php namespace Modules\\'.$name.'\\Providers; final class ModuleServiceProvider extends \\App\\Support\\Modules\\ModuleServiceProvider {}');
        file_put_contents($path.'/'.$id.'/routes/api.php', '<?php \\Illuminate\\Support\\Facades\\Route::get("records", fn () => "ok")->middleware("can:'.$id.'.record.view");');
        file_put_contents($path.'/'.$id.'/lang/fa.json', json_encode([$id.'.title' => $id], JSON_THROW_ON_ERROR));
    }
    (new ModuleManager($app, ModuleCatalog::scan($path), $loader, $path))->registerEnabled(['fixture-resources']);
    $uris = array_map(fn ($route) => $route->uri(), $app['router']->getRoutes()->getRoutes());
    resourceCheck(in_array('api/fixture-resources/records', $uris, true), 'Enabled routes must load under module prefix');
    resourceCheck(! in_array('api/fixture-off/records', $uris, true), 'Disabled routes must not load');
    foreach ($app['router']->getRoutes()->getRoutes() as $route) {
        if ($route->uri() === 'api/fixture-resources/records') {
            foreach (['api', 'auth:sanctum', 'company.context', 'can:fixture-resources.record.view'] as $guard) {
                resourceCheck(in_array($guard, $route->middleware(), true), 'Missing module route guard: '.$guard);
            }
        }
    }
    $paths = $app['migrator']->paths();
    resourceCheck(in_array($path.'/fixture-resources/database/migrations', $paths, true), 'Enabled migrations must load');
    resourceCheck(! in_array($path.'/fixture-off/database/migrations', $paths, true), 'Disabled migrations must not load');
    resourceCheck($app['translator']->get('fixture-resources.title', [], 'fa') === 'fixture-resources', 'Enabled JSON translations must load');
    resourceCheck($app['translator']->get('fixture-off.title', [], 'fa') === 'fixture-off.title', 'Disabled JSON translations must not load');
    echo "Module-owned resource isolation tests passed.\n";
} finally {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
    }
    rmdir($path);
}
