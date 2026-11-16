<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\Modules\ModuleCatalog;
use App\Support\Modules\ModuleManager;
use App\Support\Tenancy\AuthorizedCompanyContextResolver;
use App\Support\Tenancy\CompanyContext;
use App\Support\Tenancy\DenyCompanyContextResolver;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CompanyContext::class);
        $this->app->bind(AuthorizedCompanyContextResolver::class, DenyCompanyContextResolver::class);
        $this->app->singleton(ModuleCatalog::class, fn () => ModuleCatalog::scan(base_path('modules')));
        $this->app->singleton(ModuleManager::class, function ($app) {
            $loader = require base_path('vendor/autoload.php');

            return new ModuleManager($app, $app->make(ModuleCatalog::class), $loader, base_path('modules'));
        });
        $this->app->make(ModuleManager::class)->registerEnabled(config('erp-modules.enabled', []));
    }
}
