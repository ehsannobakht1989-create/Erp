<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\WarrantyAfterSales\Services\WarrantyAfterSalesService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['warranty-after-sales'], false);
    $svc = new WarrantyAfterSalesService($db);
    assertTrue($svc instanceof WarrantyAfterSalesService);
};
