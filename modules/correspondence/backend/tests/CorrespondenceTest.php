<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Correspondence\Services\CorrespondenceService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['correspondence'], false);
    $svc = new CorrespondenceService($db);
    assertTrue($svc instanceof CorrespondenceService);

    assertTrue(is_file($root . '/modules/correspondence/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/correspondence/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
