<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\PersonalAccessToken;
use App\Support\Ai\AiGateway;
use App\Support\Ai\AiToolRegistry;
use App\Support\Ai\AvalAiProvider;
use App\Support\Ai\PythonServiceProvider;
use App\Support\Auth\PermissionRegistry;
use App\Support\Contracts\AiGatewayContract;
use App\Support\Events\DomainEventBus;
use App\Support\Events\EventSubscriptions;
use App\Support\Events\InboxProcessor;
use App\Support\Modules\ManifestValidator;
use App\Support\Modules\ModuleCatalog;
use App\Support\Modules\ModuleManager;
use App\Support\Modules\ModuleState;
use App\Support\Settings\SettingsService;
use App\Support\Tenancy\AuthorizedCompanyContextResolver;
use App\Support\Tenancy\CompanyContext;
use App\Support\Tenancy\DenyCompanyContextResolver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

final class AppServiceProvider extends ServiceProvider
{
    public function boot(PermissionRegistry $permissions, Gate $gate): void
    {
        $permissions->register($gate);
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        RateLimiter::for('core-login', fn (Request $request) => Limit::perMinute(5)->by(mb_strtolower(trim((string) $request->input('email'))).'|'.$request->ip()));
    }

    public function register(): void
    {
        $this->app->singleton(CompanyContext::class);
        $this->app->singleton(EventSubscriptions::class, fn ($app) => new EventSubscriptions($app->make(ModuleCatalog::class)));
        $this->app->bind(DomainEventBus::class, fn ($app) => new DomainEventBus($app['db']->connection(), $app['events'], $app->make(CompanyContext::class), $app->make(EventSubscriptions::class)));
        $this->app->bind(InboxProcessor::class, fn ($app) => new InboxProcessor($app['db']->connection(), $app->make(EventSubscriptions::class), $app->make(CompanyContext::class), (int) config('erp.events.max_attempts', 5), (int) config('erp.events.base_delay_seconds', 30), (int) config('erp.events.stale_after_seconds', 900)));
        $this->app->bind(AuthorizedCompanyContextResolver::class, DenyCompanyContextResolver::class);
        $this->app->singleton(ModuleCatalog::class, function () {
            $errors = (new ManifestValidator)->validate(base_path('modules'), base_path('docs/module.schema.json'));
            if ($errors !== []) {
                throw new \RuntimeException(implode("\n", $errors));
            }

            return ModuleCatalog::scan(base_path('modules'));
        });
        $this->app->singleton(ModuleState::class, fn ($app) => new ModuleState($app->make(ModuleCatalog::class), $app['db']->connection(), config('erp-modules.enabled', []), base_path('modules')));
        $this->app->singleton(ModuleManager::class, function ($app) {
            $loader = require base_path('vendor/autoload.php');

            return new ModuleManager($app, $app->make(ModuleCatalog::class), $loader, base_path('modules'));
        });
        $this->app->singleton(PermissionRegistry::class, fn ($app) => new PermissionRegistry($app->make(ModuleCatalog::class), $app->make(ModuleState::class)->enabled()));
        $this->app->bind(SettingsService::class, fn ($app) => new SettingsService($app->make(ModuleCatalog::class), $app->make(ModuleState::class)->enabled(), $app['db']->connection(), $app->make(CompanyContext::class)));
        $this->app->singleton(AiToolRegistry::class, fn ($app) => new AiToolRegistry($app->make(ModuleCatalog::class)));
        $this->app->bind(AiGatewayContract::class, function ($app) {
            $config = (array) config('erp.ai');
            $http = $app->make(HttpFactory::class);

            return new AiGateway($app->make(ModuleCatalog::class), $app->make(ModuleState::class)->enabled(), $app['db']->connection(), $app->make(CompanyContext::class), $app->make(AiToolRegistry::class), [
                'avalai' => new AvalAiProvider($http, (string) $config['avalai']['base_url'], (string) $config['avalai']['api_key'], (string) $config['avalai']['model'], (int) $config['avalai']['timeout']),
                'python' => new PythonServiceProvider($http, (string) $config['python']['url'], (string) $config['python']['secret'], (int) $config['python']['timeout']),
            ], $config);
        });
        $this->app->make(ModuleManager::class)->registerEnabled($this->app->make(ModuleState::class)->enabled());
    }
}
