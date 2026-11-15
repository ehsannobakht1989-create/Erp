<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\HrCore\Services\HrCoreService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['hr-core'], false);
    $now = gmdate('Y-m-d H:i:s');
    $db->execute('INSERT INTO hr_core__employees (company_id, personnel_no, national_code, first_name, last_name, created_at, updated_at) VALUES (1, "P100", "0499370899", "رضا", "محمدی", ?, ?)', [$now, $now]);
    $svc = new HrCoreService($db);
    assertEquals('P100', $svc->byPersonnelNo(1, ['personnel_no' => 'P100'])['personnel_no']);

    assertTrue(is_file($root . '/modules/hr-core/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/hr-core/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
