<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\PurchaseOrder\Services\PurchaseOrderService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['purchase-order'], false);
    $svc = new PurchaseOrderService($db);
    assertTrue($svc instanceof PurchaseOrderService);
};
