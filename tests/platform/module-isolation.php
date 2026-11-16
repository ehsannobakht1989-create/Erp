<?php

declare(strict_types=1);

$loader = require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Http\Controllers\Core\MetaController;
use App\Support\Ai\AiToolRegistry;
use App\Support\Auth\PermissionRegistry;
use App\Support\Contracts\PermissionContract;
use App\Support\Datasets\DatasetRegistry;
use App\Support\Events\EventSubscriptions;
use App\Support\Modules\ModuleCatalog;
use App\Support\Modules\ModuleManager;
use App\Support\Tenancy\CompanyContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

function isolationCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$path = sys_get_temp_dir().'/erp-isolation-'.bin2hex(random_bytes(8));
mkdir($path);
try {
    foreach (['iso-on', 'iso-off', 'iso-rogue'] as $id) {
        $name = implode('', array_map('ucfirst', explode('-', $id)));
        foreach (['Providers', 'src/Listeners', 'src/Datasets', 'src/Ai'] as $directory) {
            mkdir($path.'/'.$id.'/'.$directory, 0777, true);
        }
        file_put_contents($path.'/'.$id.'/module.json', json_encode([
            'id' => $id, 'requires' => [], 'optional' => [],
            'permissions' => [$id.'.record.view', $id.'.secret.view'],
            'listens' => ['iso-source.record.created'], 'datasets' => [$id.'.records'],
            'menu' => [
                ['id' => $id, 'title' => ['fa' => 'رکوردها'], 'route' => '/'.$id, 'permission' => $id.'.record.view'],
                ['id' => $id.'-secret', 'title' => ['fa' => 'محرمانه'], 'route' => '/'.$id.'/secret', 'permission' => $id.'.secret.view'],
            ],
            'ai' => ['enabled_default' => false, 'capabilities' => [['id' => 'assistant', 'provider' => 'avalai', 'tools' => [$id.'.lookup']]]],
        ], JSON_THROW_ON_ERROR));
        $ns = 'Modules\\'.$name;
        file_put_contents($path.'/'.$id.'/src/Listeners/RecordHandler.php', "<?php namespace $ns\\Listeners; final class RecordHandler implements \\App\\Support\\Events\\DomainEventHandler { public function handle(\\App\\Support\\Events\\DomainEvent \$event): void {} }");
        file_put_contents($path.'/'.$id.'/src/Datasets/Records.php', "<?php namespace $ns\\Datasets; final class Records implements \\App\\Support\\Datasets\\DatasetProvider { public function definition(): array { return ['permission' => '$id.record.view', 'columns' => ['id' => ['type' => 'int']]]; } public function baseQuery(): \\Illuminate\\Database\\Query\\Builder { return \\Illuminate\\Support\\Facades\\DB::table('x'); } }");
        file_put_contents($path.'/'.$id.'/src/Ai/Lookup.php', "<?php namespace $ns\\Ai; final class Lookup implements \\App\\Support\\Ai\\AiTool { public function describe(): array { return ['description' => 'x', 'parameters' => []]; } public function run(int \$companyId, int \$userId, array \$arguments): mixed { return null; } }");
        $datasetId = $id === 'iso-rogue' ? 'iso-on.records' : $id.'.records';
        file_put_contents($path.'/'.$id.'/Providers/ModuleServiceProvider.php', "<?php namespace $ns\\Providers; final class ModuleServiceProvider extends \\App\\Support\\Modules\\ModuleServiceProvider {
            protected function listeners(): array { return ['on_record' => ['event' => 'iso-source.record.created', 'handler' => \\$ns\\Listeners\\RecordHandler::class]]; }
            protected function datasets(): array { return ['$datasetId' => \\$ns\\Datasets\\Records::class]; }
            protected function aiTools(): array { return ['$id.lookup' => ['permission' => '$id.record.view', 'handler' => \\$ns\\Ai\\Lookup::class]]; }
        }");
    }
    $catalog = ModuleCatalog::scan($path);
    $app->instance(ModuleCatalog::class, $catalog);
    foreach ([EventSubscriptions::class, DatasetRegistry::class, AiToolRegistry::class, PermissionRegistry::class] as $service) {
        $app->forgetInstance($service);
    }
    $manager = new ModuleManager($app, $catalog, $loader, $path);
    $app->instance(ModuleManager::class, $manager);
    $manager->registerEnabled(['iso-on']);
    isolationCheck($manager->enabled() === ['iso-on'], 'Boot-time enabled set must be exposed');
    $subscriptions = $app->make(EventSubscriptions::class);
    isolationCheck($subscriptions->listeners() === ['iso-on.on_record'] && $subscriptions->listenersFor('iso-source.record.created') === ['iso-on.on_record'], 'Only enabled modules may contribute listeners');
    $context = $app->make(CompanyContext::class);
    $context->activate(7);
    $app->instance(PermissionContract::class, new class implements PermissionContract
    {
        public function can(int $userId, int $companyId, string $permission): bool
        {
            return $userId === 3 && $companyId === 7 && in_array($permission, ['iso-on.record.view', 'iso-off.record.view', 'iso-off.secret.view'], true);
        }
    });
    isolationCheck($app->make(DatasetRegistry::class)->available(3) === ['iso-on.records'], 'Only enabled modules may contribute datasets');
    isolationCheck($app->make(AiToolRegistry::class)->has('iso-on.lookup') && ! $app->make(AiToolRegistry::class)->has('iso-off.lookup'), 'Only enabled modules may contribute AI tools');
    isolationCheck($app->make(PermissionRegistry::class)->all() === ['iso-on.record.view', 'iso-on.secret.view'], 'Only enabled modules may contribute permissions');
    $request = Request::create('/api/core/meta');
    $request->setUserResolver(fn () => new class implements Authenticatable
    {
        public function getAuthIdentifierName(): string
        {
            return 'id';
        }

        public function getAuthIdentifier(): int
        {
            return 3;
        }

        public function getAuthPasswordName(): string
        {
            return 'password';
        }

        public function getAuthPassword(): string
        {
            return '';
        }

        public function getRememberToken(): string
        {
            return '';
        }

        public function setRememberToken($value): void {}

        public function getRememberTokenName(): string
        {
            return '';
        }
    });
    $meta = json_decode((string) $app->call(MetaController::class, ['request' => $request])->getContent(), true, 512, JSON_THROW_ON_ERROR)['data'];
    isolationCheck(array_column($meta['modules'], 'id') === ['iso-on'], 'Meta must list only loaded modules');
    isolationCheck(array_column($meta['menu'], 'id') === ['iso-on'] && $meta['permissions'] === ['iso-on.record.view'] && $meta['company_id'] === 7, 'Menu must exclude disabled modules and entries without permission: '.json_encode($meta, JSON_UNESCAPED_UNICODE));
    $rogueApp = require dirname(__DIR__, 2).'/bootstrap/app.php';
    $rogueApp->make(Kernel::class)->bootstrap();
    $rogueApp->instance(ModuleCatalog::class, $catalog);
    foreach ([EventSubscriptions::class, DatasetRegistry::class, AiToolRegistry::class] as $service) {
        $rogueApp->forgetInstance($service);
    }
    try {
        (new ModuleManager($rogueApp, $catalog, $loader, $path))->registerEnabled(['iso-rogue']);
        $rogueApp->boot();
        throw new LogicException('A module registered another module\'s dataset');
    } catch (RuntimeException $e) {
        isolationCheck(str_contains($e->getMessage(), 'may only register its own iso-on.records'), 'Unexpected rogue failure: '.$e->getMessage());
    }
    echo "Disabled-module isolation for listeners, datasets, AI tools, permissions and menus tests passed.\n";
} finally {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
    }
    rmdir($path);
}
