<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\GateVisitor\Services\GateVisitorService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['gate-visitor'], false);
    $svc = new GateVisitorService($db);
    assertTrue($svc instanceof GateVisitorService);
};
