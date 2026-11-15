<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ReportBi\Services\ReportBiService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['report-bi'], false);
    $svc = new ReportBiService($db);
    assertTrue($svc instanceof ReportBiService);
};
