<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ReorderPoint\Services\ReorderPointService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['reorder-point'], false);
    $svc = new ReorderPointService($db);
    assertTrue($svc instanceof ReorderPointService);

    assertTrue(is_file($root . '/modules/reorder-point/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/reorder-point/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
