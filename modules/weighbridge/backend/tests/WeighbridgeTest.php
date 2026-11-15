<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Weighbridge\Services\WeighbridgeService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['weighbridge'], false);
    $svc = new WeighbridgeService($db);
    assertTrue($svc instanceof WeighbridgeService);
};
