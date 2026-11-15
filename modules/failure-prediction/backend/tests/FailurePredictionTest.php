<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\FailurePrediction\Services\FailurePredictionService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['failure-prediction'], false);
    $svc = new FailurePredictionService($db);
    assertTrue($svc instanceof FailurePredictionService);
};
