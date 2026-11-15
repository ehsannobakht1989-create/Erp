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
};
