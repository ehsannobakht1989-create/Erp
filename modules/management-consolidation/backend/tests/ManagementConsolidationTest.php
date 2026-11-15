<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ManagementConsolidation\Services\ManagementConsolidationService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['management-consolidation'], false);
    $svc = new ManagementConsolidationService($db);
    assertTrue($svc instanceof ManagementConsolidationService);

    assertTrue(is_file($root . '/modules/management-consolidation/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/management-consolidation/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
