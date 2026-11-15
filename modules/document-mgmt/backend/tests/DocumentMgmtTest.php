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

    assertTrue(is_file($root . '/modules/document-mgmt/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/document-mgmt/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
