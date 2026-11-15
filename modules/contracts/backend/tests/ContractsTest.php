<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Contracts\Services\ContractsService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['contracts'], false);
    $svc = new ContractsService($db);
    assertTrue($svc instanceof ContractsService);
};
