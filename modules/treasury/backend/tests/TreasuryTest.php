<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Treasury\Services\TreasuryService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['treasury'], false);
    $svc = new TreasuryService($db);
    assertTrue($svc instanceof TreasuryService);
};
