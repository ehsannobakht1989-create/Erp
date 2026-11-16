<?php

declare(strict_types=1);

// Merged over Laravel defaults; platform-owned tables use the core_ prefix.
return [
    'migrations' => ['table' => 'core_migrations', 'update_date_on_publish' => true],
];
