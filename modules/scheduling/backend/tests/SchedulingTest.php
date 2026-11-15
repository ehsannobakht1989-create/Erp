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
};
