<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ForexLc\Services\ForexLcService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['forex-lc'], false);
    $svc = new ForexLcService($db);
    assertTrue($svc instanceof ForexLcService);
};
