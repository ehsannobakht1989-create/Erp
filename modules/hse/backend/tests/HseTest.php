<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Hse\Services\HseService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['hse'], false);
    $svc = new HseService($db);
    assertTrue($svc instanceof HseService);
};
