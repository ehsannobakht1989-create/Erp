<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Crm\Services\CrmService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['crm'], false);
    $svc = new CrmService($db);
    assertTrue($svc instanceof CrmService);
};
