<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Mps\Services\MpsService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['mps'], false);
    $svc = new MpsService($db);
    assertTrue($svc instanceof MpsService);
};
