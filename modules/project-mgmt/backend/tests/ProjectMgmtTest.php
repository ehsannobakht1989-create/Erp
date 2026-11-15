<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ProjectMgmt\Services\ProjectMgmtService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['project-mgmt'], false);
    $svc = new ProjectMgmtService($db);
    assertTrue($svc instanceof ProjectMgmtService);
};
