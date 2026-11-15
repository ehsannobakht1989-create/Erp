<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\QualityInspection\Services\QualityInspectionService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['quality-inspection'], false);
    $svc = new QualityInspectionService($db);
    assertTrue($svc instanceof QualityInspectionService);
};
