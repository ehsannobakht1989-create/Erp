<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Payroll\Services\PayrollService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['payroll'], false);
    $svc = new PayrollService($db);
    assertTrue($svc instanceof PayrollService);
};
