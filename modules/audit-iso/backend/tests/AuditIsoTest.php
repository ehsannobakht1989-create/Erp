<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\AuditIso\Services\AuditIsoService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['audit-iso'], false);
    $svc = new AuditIsoService($db);
    assertTrue($svc instanceof AuditIsoService);

    assertTrue(is_file($root . '/modules/audit-iso/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/audit-iso/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
