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
};
