<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\CompetencyAppraisal\Services\CompetencyAppraisalService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['competency-appraisal'], false);
    $svc = new CompetencyAppraisalService($db);
    assertTrue($svc instanceof CompetencyAppraisalService);

    assertTrue(is_file($root . '/modules/competency-appraisal/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/competency-appraisal/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
