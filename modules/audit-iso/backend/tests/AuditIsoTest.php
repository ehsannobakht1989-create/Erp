<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\AuditIso\Services\AuditIsoService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['audit-iso'], false);
    $svc = new AuditIsoService($db);
    assertTrue($svc instanceof AuditIsoService);
};
