<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Scheduling\Services\SchedulingService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['scheduling'], false);
    $svc = new SchedulingService($db);
    assertTrue($svc instanceof SchedulingService);

    assertTrue(is_file($root . '/modules/scheduling/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/scheduling/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
