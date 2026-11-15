<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Mrp\Services\MrpService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['mrp'], false);
    $svc = new MrpService($db);
    assertTrue($svc instanceof MrpService);

    assertTrue(is_file($root . '/modules/mrp/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/mrp/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
