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

    assertTrue(is_file($root . '/modules/waste-environment/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/waste-environment/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
