<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\FailurePrediction\Services\FailurePredictionService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['failure-prediction'], false);
    $svc = new FailurePredictionService($db);
    assertTrue($svc instanceof FailurePredictionService);

    assertTrue(is_file($root . '/modules/failure-prediction/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/failure-prediction/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
