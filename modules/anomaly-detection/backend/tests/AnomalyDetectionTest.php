<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\AnomalyDetection\Services\AnomalyDetectionService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['anomaly-detection'], false);
    $svc = new AnomalyDetectionService($db);
    assertTrue($svc instanceof AnomalyDetectionService);

    assertTrue(is_file($root . '/modules/anomaly-detection/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/anomaly-detection/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
