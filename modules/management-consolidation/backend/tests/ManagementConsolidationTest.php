<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ManagementConsolidation\Services\ManagementConsolidationService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['management-consolidation'], false);
    $svc = new ManagementConsolidationService($db);
    assertTrue($svc instanceof ManagementConsolidationService);
};
