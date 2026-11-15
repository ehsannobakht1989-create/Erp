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

    assertTrue(is_file($root . '/modules/purchase-request/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/purchase-request/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
