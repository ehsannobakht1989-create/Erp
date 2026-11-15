<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\AuditLog\AuditService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['audit-log'], false);

    $audit = new AuditService($db);
    $audit->record(1, 1, 'inventory', 'inventory__receipt', 'insert', 10, null, ['qty' => 5]);
    $audit->record(1, 1, 'inventory', 'inventory__receipt', 'update', 10, ['qty' => 5], ['qty' => 8]);
    assertTrue($audit->verifyChain(), 'Audit hash chain should verify');
};
