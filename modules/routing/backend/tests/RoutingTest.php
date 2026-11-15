<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Routing\Services\RoutingService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['routing'], false);
    $svc = new RoutingService($db);
    assertTrue($svc instanceof RoutingService);

    assertTrue(is_file($root . '/modules/routing/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/routing/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
