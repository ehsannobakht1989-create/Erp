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
};
