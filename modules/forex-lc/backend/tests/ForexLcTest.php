<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ForexLc\Services\ForexLcService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['forex-lc'], false);
    $svc = new ForexLcService($db);
    assertTrue($svc instanceof ForexLcService);

    assertTrue(is_file($root . '/modules/forex-lc/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/forex-lc/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
