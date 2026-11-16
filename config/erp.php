<?php

declare(strict_types=1);

return [
    'display_timezone' => env('ERP_DISPLAY_TIMEZONE', 'Asia/Tehran'),
    'ai' => [
        'enabled' => (bool) env('ERP_AI_ENABLED', false),
        'kill_switch' => (bool) env('AI_KILL_SWITCH', false),
        'max_tokens' => (int) env('ERP_AI_MAX_TOKENS', 1024),
        'redact' => ['password', 'token', 'api_key', 'secret', 'national_id', 'national_code', 'iban', 'card_number', 'mobile', 'phone', 'email'],
        'avalai' => ['base_url' => env('AVALAI_BASE_URL', 'https://api.avalai.ir/v1'), 'api_key' => (string) env('AVALAI_API_KEY', ''), 'model' => env('AVALAI_MODEL', 'gpt-4o-mini'), 'timeout' => 30],
        'python' => ['url' => env('AI_SERVICE_URL', 'http://127.0.0.1:8000'), 'secret' => (string) env('AI_SERVICE_SECRET', ''), 'timeout' => 30],
        'prices' => [
            'avalai' => ['input_per_1k' => env('ERP_AI_AVALAI_INPUT_PER_1K', '0'), 'output_per_1k' => env('ERP_AI_AVALAI_OUTPUT_PER_1K', '0')],
            'python' => ['input_per_1k' => '0', 'output_per_1k' => '0'],
        ],
    ],
    'events' => [
        'max_attempts' => (int) env('ERP_EVENTS_MAX_ATTEMPTS', 5),
        'base_delay_seconds' => (int) env('ERP_EVENTS_BASE_DELAY', 30),
        'stale_after_seconds' => (int) env('ERP_EVENTS_STALE_AFTER', 900),
        'prune_days' => (int) env('ERP_EVENTS_PRUNE_DAYS', 30),
    ],
];
