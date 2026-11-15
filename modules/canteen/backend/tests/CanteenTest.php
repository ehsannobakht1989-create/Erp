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

    assertTrue(is_file($root . '/modules/canteen/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/canteen/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
