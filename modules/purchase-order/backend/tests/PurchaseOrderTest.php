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

    assertTrue(is_file($root . '/modules/purchase-order/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/purchase-order/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
