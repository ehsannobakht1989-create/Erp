<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Recruitment\Services\RecruitmentService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['recruitment'], false);
    $svc = new RecruitmentService($db);
    assertTrue($svc instanceof RecruitmentService);
};
