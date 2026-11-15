<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Routing\Services\RoutingService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['routing'], false);
    $svc = new RoutingService($db);
    assertTrue($svc instanceof RoutingService);
};
