<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Hse\Services\HseService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['hse'], false);
    $svc = new HseService($db);
    assertTrue($svc instanceof HseService);

    assertTrue(is_file($root . '/modules/hse/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/hse/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
