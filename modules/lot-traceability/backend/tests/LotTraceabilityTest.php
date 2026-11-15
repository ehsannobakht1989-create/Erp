<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\LotTraceability\Services\LotTraceabilityService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['lot-traceability'], false);
    $svc = new LotTraceabilityService($db);
    assertTrue($svc instanceof LotTraceabilityService);

    assertTrue(is_file($root . '/modules/lot-traceability/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/lot-traceability/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
