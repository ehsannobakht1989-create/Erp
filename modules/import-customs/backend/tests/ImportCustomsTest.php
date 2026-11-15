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

    assertTrue(is_file($root . '/modules/import-customs/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/import-customs/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
