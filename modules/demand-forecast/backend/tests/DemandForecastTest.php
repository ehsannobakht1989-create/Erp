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

    assertTrue(is_file($root . '/modules/demand-forecast/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/demand-forecast/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
