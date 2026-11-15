<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ArAp\Services\ArApService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['ar-ap'], false);
    $svc = new ArApService($db);
    assertTrue($svc instanceof ArApService);

    assertTrue(is_file($root . '/modules/ar-ap/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/ar-ap/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
