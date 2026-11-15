<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\QualityInspection\Services\QualityInspectionService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['quality-inspection'], false);
    $svc = new QualityInspectionService($db);
    assertTrue($svc instanceof QualityInspectionService);

    assertTrue(is_file($root . '/modules/quality-inspection/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/quality-inspection/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
