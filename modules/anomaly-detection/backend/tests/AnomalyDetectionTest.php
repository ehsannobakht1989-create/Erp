<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\AnomalyDetection\Services\AnomalyDetectionService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['anomaly-detection'], false);
    $svc = new AnomalyDetectionService($db);
    assertTrue($svc instanceof AnomalyDetectionService);
};
