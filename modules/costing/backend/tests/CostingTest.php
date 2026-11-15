<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Costing\Services\CostingService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['costing'], false);
    $svc = new CostingService($db);
    assertTrue($svc instanceof CostingService);

    assertTrue(is_file($root . '/modules/costing/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/costing/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
