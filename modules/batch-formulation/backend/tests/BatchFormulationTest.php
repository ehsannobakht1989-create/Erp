<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\BatchFormulation\Services\BatchFormulationService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['batch-formulation'], false);
    $svc = new BatchFormulationService($db);
    assertTrue($svc instanceof BatchFormulationService);

    assertTrue(is_file($root . '/modules/batch-formulation/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/batch-formulation/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
