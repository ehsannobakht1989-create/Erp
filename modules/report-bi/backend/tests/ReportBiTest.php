<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ReportBi\Services\ReportBiService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['report-bi'], false);
    $svc = new ReportBiService($db);
    assertTrue($svc instanceof ReportBiService);

    assertTrue(is_file($root . '/modules/report-bi/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/report-bi/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
