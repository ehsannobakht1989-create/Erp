<?php

declare(strict_types=1);

return ['driver' => env('SESSION_DRIVER', 'file'), 'table' => 'core_sessions', 'encrypt' => true, 'http_only' => true, 'same_site' => 'lax'];
