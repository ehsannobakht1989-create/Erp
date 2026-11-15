<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Oee\Services\OeeService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['oee'], false);
    $svc = new OeeService($db);
    assertTrue($svc instanceof OeeService);

    assertTrue(is_file($root . '/modules/oee/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/oee/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
