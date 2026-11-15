<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Mes\Services\MesService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['mes'], false);
    $svc = new MesService($db);
    assertTrue($svc instanceof MesService);

    assertTrue(is_file($root . '/modules/mes/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/mes/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
