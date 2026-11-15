<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\TaxMoadian\Services\TaxMoadianService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['tax-moadian'], false);
    $svc = new TaxMoadianService($db);
    assertTrue($svc instanceof TaxMoadianService);
};
