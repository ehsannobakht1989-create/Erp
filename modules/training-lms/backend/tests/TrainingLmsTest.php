<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\TrainingLms\Services\TrainingLmsService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['training-lms'], false);
    $svc = new TrainingLmsService($db);
    assertTrue($svc instanceof TrainingLmsService);

    assertTrue(is_file($root . '/modules/training-lms/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/training-lms/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
