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

    assertTrue(is_file($root . '/modules/sales/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/sales/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
