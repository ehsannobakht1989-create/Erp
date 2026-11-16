<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\PersonalAccessToken;
use App\Support\Auth\PermissionRegistry;
use App\Support\Events\DomainEventBus;
use App\Support\Modules\ManifestValidator;
use App\Support\Modules\ModuleCatalog;
use App\Support\Modules\ModuleManager;
use App\Support\Modules\ModuleState;
use App\Support\Tenancy\AuthorizedCompanyContextResolver;
use App\Support\Tenancy\CompanyContext;
use App\Support\Tenancy\DenyCompanyContextResolver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Auth\Access\Gate;
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
        $this->app->bind(DomainEventBus::class, fn ($app) => new DomainEventBus($app['db']->connection(), $app['events'], $app->make(CompanyContext::class)));
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
        $this->app->make(ModuleManager::class)->registerEnabled($this->app->make(ModuleState::class)->enabled());
    }
}
