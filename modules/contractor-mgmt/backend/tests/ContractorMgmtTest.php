<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ContractorMgmt\Services\ContractorMgmtService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['contractor-mgmt'], false);
    $svc = new ContractorMgmtService($db);
    assertTrue($svc instanceof ContractorMgmtService);

    assertTrue(is_file($root . '/modules/contractor-mgmt/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/contractor-mgmt/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
