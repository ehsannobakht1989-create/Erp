<?php

declare(strict_types=1);

// Merged over Laravel defaults. Persist UTC; present dates in erp.display_timezone.
return [
    'timezone' => 'UTC',
    'locale' => env('APP_LOCALE', 'fa'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'fa'),
];
