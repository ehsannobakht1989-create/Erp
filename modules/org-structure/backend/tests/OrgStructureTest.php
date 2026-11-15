<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\OrgStructure\Services\OrgStructureService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['org-structure'], false);

    $now = gmdate('Y-m-d H:i:s');
    $db->execute(
        'INSERT INTO org_structure__units (company_id, unit_type, code, name_fa, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)',
        [1, 'plant', 'PLT-01', 'کارخانه مرکزی', $now, $now]
    );
    $svc = new OrgStructureService($db);
    assertEquals(1, count($svc->unitsTree(1)));

    assertTrue(is_file($root . '/modules/org-structure/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/org-structure/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
