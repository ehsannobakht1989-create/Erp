<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ConditionMonitoringIot\Services\ConditionMonitoringIotService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['condition-monitoring-iot'], false);
    $svc = new ConditionMonitoringIotService($db);
    assertTrue($svc instanceof ConditionMonitoringIotService);
};
