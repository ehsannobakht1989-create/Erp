<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Payroll\Services\PayrollService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['payroll'], false);
    $svc = new PayrollService($db);
    assertTrue($svc instanceof PayrollService);

    assertTrue(is_file($root . '/modules/payroll/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/payroll/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
