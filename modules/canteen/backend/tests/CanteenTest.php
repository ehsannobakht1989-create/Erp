<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Canteen\Services\CanteenService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['canteen'], false);
    $svc = new CanteenService($db);
    assertTrue($svc instanceof CanteenService);
};
