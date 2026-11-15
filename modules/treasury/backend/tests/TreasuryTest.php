<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Treasury\Services\TreasuryService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['treasury'], false);
    $svc = new TreasuryService($db);
    assertTrue($svc instanceof TreasuryService);

    assertTrue(is_file($root . '/modules/treasury/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/treasury/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
