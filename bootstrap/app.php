<?php

declare(strict_types=1);

use App\Console\Commands\EventsDrain;
use App\Console\Commands\EventsPrune;
use App\Console\Commands\EventsRetry;
use App\Console\Commands\ModulesDisable;
use App\Console\Commands\ModulesGraph;
use App\Console\Commands\ModulesList;
use App\Console\Commands\ModulesSetState;
use App\Console\Commands\ModulesValidate;
use App\Support\Http\ApiExceptionRenderer;
use App\Support\Tenancy\CompanyContextMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// The legacy public entry point remains in use until the platform is ready.
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(api: __DIR__.'/../routes/api.php', commands: __DIR__.'/../routes/console.php', health: '/up', then: fn () => Route::group([], __DIR__.'/../routes/install.php'))
    ->withCommands([EventsDrain::class, EventsRetry::class, EventsPrune::class, ModulesList::class, ModulesGraph::class, ModulesSetState::class, ModulesDisable::class, ModulesValidate::class])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->alias(['company.context' => CompanyContextMiddleware::class]);
    })
    ->withExceptions(fn (Exceptions $exceptions) => $exceptions->render(fn (Throwable $e, Request $request) => ApiExceptionRenderer::render($e, $request)))
    ->create();
