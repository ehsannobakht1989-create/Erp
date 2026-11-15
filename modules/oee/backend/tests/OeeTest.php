<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Oee\Services\OeeService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['oee'], false);
    $svc = new OeeService($db);
    assertTrue($svc instanceof OeeService);
};
