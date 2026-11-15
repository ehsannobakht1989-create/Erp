<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ReorderPoint\Services\ReorderPointService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['reorder-point'], false);
    $svc = new ReorderPointService($db);
    assertTrue($svc instanceof ReorderPointService);
};
