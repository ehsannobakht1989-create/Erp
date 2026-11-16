<?php

declare(strict_types=1);

return [
    'display_timezone' => env('ERP_DISPLAY_TIMEZONE', 'Asia/Tehran'),
    'events' => [
        'max_attempts' => (int) env('ERP_EVENTS_MAX_ATTEMPTS', 5),
        'base_delay_seconds' => (int) env('ERP_EVENTS_BASE_DELAY', 30),
        'stale_after_seconds' => (int) env('ERP_EVENTS_STALE_AFTER', 900),
        'prune_days' => (int) env('ERP_EVENTS_PRUNE_DAYS', 30),
    ],
];
