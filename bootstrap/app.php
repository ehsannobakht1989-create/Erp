<?php

declare(strict_types=1);

use App\Console\Commands\ModulesList;
use Illuminate\Foundation\Application;

// The legacy public entry point remains in use until the platform is ready.
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(health: '/up', commands: __DIR__.'/../routes/console.php')
    ->withCommands([ModulesList::class])
    ->create();
