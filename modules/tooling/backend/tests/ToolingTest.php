<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Tooling\Services\ToolingService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['tooling'], false);
    $svc = new ToolingService($db);
    assertTrue($svc instanceof ToolingService);
};
