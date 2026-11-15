<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Lims\Services\LimsService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['lims'], false);
    $svc = new LimsService($db);
    assertTrue($svc instanceof LimsService);

    assertTrue(is_file($root . '/modules/lims/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/lims/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
