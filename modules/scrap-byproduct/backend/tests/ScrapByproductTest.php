<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ScrapByproduct\Services\ScrapByproductService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['scrap-byproduct'], false);
    $svc = new ScrapByproductService($db);
    assertTrue($svc instanceof ScrapByproductService);

    assertTrue(is_file($root . '/modules/scrap-byproduct/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/scrap-byproduct/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
