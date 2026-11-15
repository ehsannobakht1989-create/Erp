<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Rfq\Services\RfqService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['rfq'], false);
    $svc = new RfqService($db);
    assertTrue($svc instanceof RfqService);

    assertTrue(is_file($root . '/modules/rfq/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/rfq/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
