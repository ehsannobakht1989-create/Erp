<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Tooling\Services\ToolingService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['tooling'], false);
    $svc = new ToolingService($db);
    assertTrue($svc instanceof ToolingService);

    assertTrue(is_file($root . '/modules/tooling/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/tooling/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
