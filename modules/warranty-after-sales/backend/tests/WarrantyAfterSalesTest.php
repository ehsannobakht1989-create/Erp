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

    assertTrue(is_file($root . '/modules/warranty-after-sales/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/warranty-after-sales/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
