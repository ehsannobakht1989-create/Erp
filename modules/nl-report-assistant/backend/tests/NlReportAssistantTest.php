<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\NlReportAssistant\Services\NlReportAssistantService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['nl-report-assistant'], false);
    $svc = new NlReportAssistantService($db);
    assertTrue($svc instanceof NlReportAssistantService);
};
