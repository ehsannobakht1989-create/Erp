<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Mrp\Services\MrpService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['mrp'], false);
    $svc = new MrpService($db);
    assertTrue($svc instanceof MrpService);
};
