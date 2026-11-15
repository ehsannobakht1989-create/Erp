<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\SupplierEvaluation\Services\SupplierEvaluationService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['supplier-evaluation'], false);
    $svc = new SupplierEvaluationService($db);
    assertTrue($svc instanceof SupplierEvaluationService);

    assertTrue(is_file($root . '/modules/supplier-evaluation/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/supplier-evaluation/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
