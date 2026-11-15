<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\LotTraceability\Services\LotTraceabilityService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['lot-traceability'], false);
    $svc = new LotTraceabilityService($db);
    assertTrue($svc instanceof LotTraceabilityService);
};
