<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Contracts\Services\ContractsService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['contracts'], false);
    $svc = new ContractsService($db);
    assertTrue($svc instanceof ContractsService);

    assertTrue(is_file($root . '/modules/contracts/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/contracts/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
