<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\MultiCompany\NumberingService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['multi-company'], false);

    $svc = new NumberingService($db);
    assertEquals('PO-03-0001', $svc->next(1, 'PO', 'PO-{YY}-{####}', 0, '1403'));
    assertEquals('PO-03-0002', $svc->next(1, 'PO', 'PO-{YY}-{####}', 0, '1403'));
};
