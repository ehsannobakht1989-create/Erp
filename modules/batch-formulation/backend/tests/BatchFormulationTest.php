<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\BatchFormulation\Services\BatchFormulationService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['batch-formulation'], false);
    $svc = new BatchFormulationService($db);
    assertTrue($svc instanceof BatchFormulationService);
};
