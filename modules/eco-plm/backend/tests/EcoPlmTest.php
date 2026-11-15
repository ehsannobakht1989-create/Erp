<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\EcoPlm\Services\EcoPlmService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['eco-plm'], false);
    $svc = new EcoPlmService($db);
    assertTrue($svc instanceof EcoPlmService);
};
