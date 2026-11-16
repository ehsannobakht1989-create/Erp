<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

// One cron line runs everything: * * * * * php /path/artisan schedule:run
// Enabled module providers add their own schedules; disabled modules contribute none.
Schedule::command('erp:events:drain --limit=200')->everyMinute()->withoutOverlapping()->name('erp-events-drain');
Schedule::command('queue:work database --stop-when-empty --max-time=50 --tries=1')->everyMinute()->withoutOverlapping()->name('erp-queue-work');
Schedule::command('erp:events:prune')->dailyAt('02:30')->name('erp-events-prune');
