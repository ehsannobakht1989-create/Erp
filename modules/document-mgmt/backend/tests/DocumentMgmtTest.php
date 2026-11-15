<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\DocumentMgmt\DocumentService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['document-mgmt'], false);

    $svc = new DocumentService($db);
    $id = $svc->attach(1, 'purchase-order.po', 12, 'invoice.pdf', 'application/pdf', 10240, 'storage/docs/invoice.pdf');
    assertTrue($id !== '', 'Document attach should return id');
};
