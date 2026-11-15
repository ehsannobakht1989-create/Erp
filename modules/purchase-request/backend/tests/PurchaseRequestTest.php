<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\PurchaseRequest\Services\PurchaseRequestService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['purchase-request'], false);
    $svc = new PurchaseRequestService($db);
    assertTrue($svc instanceof PurchaseRequestService);
};
