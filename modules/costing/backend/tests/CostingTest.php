<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Costing\Services\CostingService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['costing'], false);
    $svc = new CostingService($db);
    assertTrue($svc instanceof CostingService);
};
