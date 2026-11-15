<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Lims\Services\LimsService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['lims'], false);
    $svc = new LimsService($db);
    assertTrue($svc instanceof LimsService);
};
