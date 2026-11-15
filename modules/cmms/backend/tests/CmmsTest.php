<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Cmms\Services\CmmsService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['cmms'], false);
    $svc = new CmmsService($db);
    assertTrue($svc instanceof CmmsService);
};
