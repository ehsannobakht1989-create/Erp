<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Rfq\Services\RfqService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['rfq'], false);
    $svc = new RfqService($db);
    assertTrue($svc instanceof RfqService);
};
