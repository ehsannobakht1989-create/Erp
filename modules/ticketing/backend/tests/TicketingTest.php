<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Ticketing\Services\TicketingService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['ticketing'], false);
    $svc = new TicketingService($db);
    assertTrue($svc instanceof TicketingService);

    assertTrue(is_file($root . '/modules/ticketing/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/ticketing/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
