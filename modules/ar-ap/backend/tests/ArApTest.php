<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ArAp\Services\ArApService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['ar-ap'], false);
    $svc = new ArApService($db);
    assertTrue($svc instanceof ArApService);
};
