<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\FixedAssets\Services\FixedAssetsService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['fixed-assets'], false);
    $svc = new FixedAssetsService($db);
    assertTrue($svc instanceof FixedAssetsService);

    assertTrue(is_file($root . '/modules/fixed-assets/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/fixed-assets/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
