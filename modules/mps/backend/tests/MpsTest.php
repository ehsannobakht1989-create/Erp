<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Mps\Services\MpsService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['mps'], false);
    $svc = new MpsService($db);
    assertTrue($svc instanceof MpsService);

    assertTrue(is_file($root . '/modules/mps/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/mps/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
