<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Modules\ModuleCatalog;
use App\Support\Modules\ModuleState;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

function stateCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
function stateReject(callable $operation, string $message): void
{
    try {
        $operation();
    } catch (RuntimeException $e) {
        stateCheck(str_contains($e->getMessage(), $message), $e->getMessage());

        return;
    }
    throw new RuntimeException('Operation should have failed: '.$message);
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
DB::purge('sqlite');
$path = sys_get_temp_dir().'/erp-state-'.bin2hex(random_bytes(8));
mkdir($path);
try {
    foreach (['base' => [], 'feature' => ['base'], 'legacy' => []] as $id => $requires) {
        mkdir($path.'/'.$id.'/Providers', 0777, true);
        file_put_contents($path.'/'.$id.'/module.json', json_encode(['id' => $id, 'requires' => $requires, 'optional' => []], JSON_THROW_ON_ERROR));
        if ($id !== 'legacy') {
            file_put_contents($path.'/'.$id.'/Providers/ModuleServiceProvider.php', '<?php');
        }
    }
    $catalog = ModuleCatalog::scan($path);
    $state = new ModuleState($catalog, DB::connection(), [], $path);
    stateCheck($state->enabled() === [], 'Before install defaults should work');
    stateReject(fn () => $state->set('base', true), 'migrations');
    $migration = require dirname(__DIR__, 2).'/database/migrations/2026_11_16_000001_create_core_module_state.php';
    $migration->up();
    stateReject(fn () => $state->set('feature', true), 'requires disabled');
    stateReject(fn () => $state->set('unknown', true), 'Unknown module');
    stateReject(fn () => $state->set('legacy', true), 'no Laravel provider');
    stateCheck(DB::table('core_modules')->count() === 0, 'Rejected changes must not persist');
    $state->set('base', true);
    $state->set('feature', true);
    stateCheck($state->enabled() === ['base', 'feature'], 'Overrides must enable in dependency order');
    stateReject(fn () => $state->set('base', false), 'requires disabled');
    stateCheck($state->enabled() === ['base', 'feature'], 'Rejected disable must roll back');
    $state->set('feature', false);
    $configured = new ModuleState($catalog, DB::connection(), ['base', 'feature'], $path);
    stateCheck($configured->enabled() === ['base'], 'Database false must override config true');
    $state->set('base', false);
    stateCheck($state->enabled() === [], 'Disable must persist');
    DB::table('core_modules')->insert(['id' => 'missing', 'enabled' => 1]);
    stateReject(fn () => $state->enabled(), 'Unknown persisted');
    DB::table('core_modules')->where('id', 'missing')->delete();
    $migration->down();
    stateCheck($state->enabled() === [], 'Rollback migration should restore config defaults');
    echo "Module state tests passed.\n";
} finally {
    foreach (glob($path.'/*/Providers/*') ?: [] as $file) {
        unlink($file);
    }
    foreach (glob($path.'/*') ?: [] as $directory) {
        unlink($directory.'/module.json');
        rmdir($directory.'/Providers');
        rmdir($directory);
    }
    rmdir($path);
}
