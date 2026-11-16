<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Modules\ModuleCatalog;
use App\Support\Modules\ModuleState;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

function commandCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
DB::purge('sqlite');
Artisan::call('migrate', ['--force' => true]);
$path = sys_get_temp_dir().'/erp-commands-'.bin2hex(random_bytes(8));
mkdir($path);
try {
    foreach (['base' => [], 'feature' => ['base']] as $id => $requires) {
        mkdir($path.'/'.$id.'/Providers', 0777, true);
        file_put_contents($path.'/'.$id.'/module.json', json_encode(['id' => $id, 'requires' => $requires, 'optional' => []], JSON_THROW_ON_ERROR));
        file_put_contents($path.'/'.$id.'/Providers/ModuleServiceProvider.php', '<?php');
    }
    $catalog = ModuleCatalog::scan($path);
    $state = new ModuleState($catalog, app('db'), [], $path);
    $app->instance(ModuleCatalog::class, $catalog);
    $app->instance(ModuleState::class, $state);
    $rejected = function (array $call, string $needle): void {
        try {
            Artisan::call(...$call);
        } catch (RuntimeException $e) {
            commandCheck(str_contains($e->getMessage(), $needle), 'Unexpected command error: '.$e->getMessage());

            return;
        }
        throw new LogicException('Command should have failed: '.json_encode($call));
    };
    $rejected(['erp:modules:enable', ['module' => 'feature']], 'requires disabled');
    commandCheck(Artisan::call('erp:modules:enable', ['module' => 'base']) === 0 && str_contains(Artisan::output(), 'next application boot'), 'Enable must succeed and explain when it applies');
    commandCheck(Artisan::call('erp:modules:enable', ['module' => 'feature']) === 0, 'Dependent enable must succeed');
    commandCheck($state->enabled() === ['base', 'feature'], 'Enable commands must persist overrides');
    $rejected(['erp:modules:disable', ['module' => 'base']], 'requires disabled');
    commandCheck(Artisan::call('erp:modules:enable', ['module' => 'feature', '--disable' => true]) === 0, 'enable --disable must succeed');
    commandCheck(Artisan::call('erp:modules:disable', ['module' => 'base']) === 0 && $state->enabled() === [], 'Disable command must persist');
    $rejected(['erp:modules:enable', ['module' => 'missing']], 'Unknown module');
    commandCheck(DB::table('core_modules')->pluck('enabled', 'id')->map(fn ($v) => (bool) $v)->all() === ['base' => false, 'feature' => false], 'Rejected commands must not persist');
    echo "Module enable/disable command tests passed.\n";
} finally {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
    }
    rmdir($path);
}
