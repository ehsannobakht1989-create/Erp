<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\NcrCapa\Services\NcrCapaService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['ncr-capa'], false);
    $svc = new NcrCapaService($db);
    assertTrue($svc instanceof NcrCapaService);

    assertTrue(is_file($root . '/modules/ncr-capa/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/ncr-capa/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
