<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Correspondence\Services\CorrespondenceService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['correspondence'], false);
    $svc = new CorrespondenceService($db);
    assertTrue($svc instanceof CorrespondenceService);
};
