<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\WasteEnvironment\Services\WasteEnvironmentService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['waste-environment'], false);
    $svc = new WasteEnvironmentService($db);
    assertTrue($svc instanceof WasteEnvironmentService);
};
