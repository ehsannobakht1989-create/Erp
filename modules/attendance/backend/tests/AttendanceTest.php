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
};
