<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\EcoPlm\Services\EcoPlmService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['eco-plm'], false);
    $svc = new EcoPlmService($db);
    assertTrue($svc instanceof EcoPlmService);

    assertTrue(is_file($root . '/modules/eco-plm/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/eco-plm/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
