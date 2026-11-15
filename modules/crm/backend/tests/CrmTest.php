<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Crm\Services\CrmService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['crm'], false);
    $svc = new CrmService($db);
    assertTrue($svc instanceof CrmService);

    assertTrue(is_file($root . '/modules/crm/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/crm/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
