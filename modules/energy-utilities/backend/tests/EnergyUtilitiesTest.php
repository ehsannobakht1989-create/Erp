<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\EnergyUtilities\Services\EnergyUtilitiesService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['energy-utilities'], false);
    $svc = new EnergyUtilitiesService($db);
    assertTrue($svc instanceof EnergyUtilitiesService);
};
