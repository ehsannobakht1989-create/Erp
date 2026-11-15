<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\WorkOrder\Services\WorkOrderService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['work-order'], false);
    $svc = new WorkOrderService($db);
    assertTrue($svc instanceof WorkOrderService);
};
