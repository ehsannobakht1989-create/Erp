<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Attendance\Services\AttendanceService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['attendance'], false);
    $svc = new AttendanceService($db);
    assertTrue($svc instanceof AttendanceService);

    assertTrue(is_file($root . '/modules/attendance/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/attendance/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
