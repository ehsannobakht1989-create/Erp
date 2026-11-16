<?php

declare(strict_types=1);

return [
    'default' => env('QUEUE_CONNECTION', 'database'),
    'connections' => [
        'database' => [
            'driver' => 'database',
            'connection' => env('DB_QUEUE_CONNECTION'),
            'table' => 'core_jobs',
            'queue' => env('DB_QUEUE', 'default'),
            'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90),
            'after_commit' => false,
        ],
    ],
    'failed' => ['driver' => 'database-uuids', 'database' => env('DB_CONNECTION', 'sqlite'), 'table' => 'core_failed_jobs'],
];
