<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ImportCustoms\Services\ImportCustomsService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['import-customs'], false);
    $svc = new ImportCustomsService($db);
    assertTrue($svc instanceof ImportCustomsService);
};
