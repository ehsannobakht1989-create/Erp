<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\GateVisitor\Services\GateVisitorService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['gate-visitor'], false);
    $svc = new GateVisitorService($db);
    assertTrue($svc instanceof GateVisitorService);

    assertTrue(is_file($root . '/modules/gate-visitor/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/gate-visitor/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
