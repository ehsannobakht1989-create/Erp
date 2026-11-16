<?php

declare(strict_types=1);

use App\Console\Commands\ModulesList;
use App\Console\Commands\ModulesGraph;
use App\Console\Commands\ModulesSetState;
use App\Console\Commands\ModulesDisable;
use App\Support\Tenancy\CompanyContextMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

// The legacy public entry point remains in use until the platform is ready.
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(health: '/up', commands: __DIR__.'/../routes/console.php')
    ->withCommands([ModulesList::class, ModulesGraph::class, ModulesSetState::class, ModulesDisable::class])
    ->withMiddleware(fn (Middleware $middleware) => $middleware->alias(['company.context' => CompanyContextMiddleware::class]))
    ->create();
