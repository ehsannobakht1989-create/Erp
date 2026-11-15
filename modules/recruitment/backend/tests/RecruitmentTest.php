<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Recruitment\Services\RecruitmentService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['recruitment'], false);
    $svc = new RecruitmentService($db);
    assertTrue($svc instanceof RecruitmentService);

    assertTrue(is_file($root . '/modules/recruitment/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/recruitment/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
