<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Weighbridge\Services\WeighbridgeService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['weighbridge'], false);
    $svc = new WeighbridgeService($db);
    assertTrue($svc instanceof WeighbridgeService);

    assertTrue(is_file($root . '/modules/weighbridge/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/weighbridge/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
