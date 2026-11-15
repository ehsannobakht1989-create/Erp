<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\DemandForecast\Services\DemandForecastService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['demand-forecast'], false);
    $svc = new DemandForecastService($db);
    assertTrue($svc instanceof DemandForecastService);
};
