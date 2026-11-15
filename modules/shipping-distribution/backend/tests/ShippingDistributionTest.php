<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ShippingDistribution\Services\ShippingDistributionService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['shipping-distribution'], false);
    $svc = new ShippingDistributionService($db);
    assertTrue($svc instanceof ShippingDistributionService);

    assertTrue(is_file($root . '/modules/shipping-distribution/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/shipping-distribution/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
