<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ParkingVehicles\Services\ParkingVehiclesService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['parking-vehicles'], false);
    $svc = new ParkingVehiclesService($db);
    assertTrue($svc instanceof ParkingVehiclesService);
};
