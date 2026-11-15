<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Budgeting\Services\BudgetingService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['budgeting'], false);
    $svc = new BudgetingService($db);
    assertTrue($svc instanceof BudgetingService);
};
