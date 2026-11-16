<?php

declare(strict_types=1);

// Merged over Laravel defaults. Private files are never served by an unauthenticated route;
// document-mgmt provides permission-checked downloads.
return [
    'disks' => [
        'local' => ['driver' => 'local', 'root' => storage_path('app/private'), 'serve' => false, 'throw' => false, 'report' => false],
    ],
];
