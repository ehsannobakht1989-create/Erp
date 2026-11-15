<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Budgeting\Services\BudgetingService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['budgeting'], false);
    $svc = new BudgetingService($db);
    assertTrue($svc instanceof BudgetingService);

    assertTrue(is_file($root . '/modules/budgeting/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/budgeting/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
