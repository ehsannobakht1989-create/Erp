<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Mes\Services\MesService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['mes'], false);
    $svc = new MesService($db);
    assertTrue($svc instanceof MesService);
};
