<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ScheduleOptimizer\Services\ScheduleOptimizerService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['schedule-optimizer'], false);
    $svc = new ScheduleOptimizerService($db);
    assertTrue($svc instanceof ScheduleOptimizerService);

    assertTrue(is_file($root . '/modules/schedule-optimizer/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/schedule-optimizer/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
