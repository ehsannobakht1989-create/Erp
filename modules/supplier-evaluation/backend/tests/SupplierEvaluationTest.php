<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\SupplierEvaluation\Services\SupplierEvaluationService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['supplier-evaluation'], false);
    $svc = new SupplierEvaluationService($db);
    assertTrue($svc instanceof SupplierEvaluationService);
};
