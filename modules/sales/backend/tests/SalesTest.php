<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Sales\Services\SalesService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['sales'], false);
    $svc = new SalesService($db);
    assertTrue($svc instanceof SalesService);
};
