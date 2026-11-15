<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ContractorMgmt\Services\ContractorMgmtService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['contractor-mgmt'], false);
    $svc = new ContractorMgmtService($db);
    assertTrue($svc instanceof ContractorMgmtService);
};
