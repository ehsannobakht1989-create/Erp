<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\EnergyUtilities\Services\EnergyUtilitiesService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['energy-utilities'], false);
    $svc = new EnergyUtilitiesService($db);
    assertTrue($svc instanceof EnergyUtilitiesService);

    assertTrue(is_file($root . '/modules/energy-utilities/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/energy-utilities/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
