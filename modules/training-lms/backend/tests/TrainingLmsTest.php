<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\TrainingLms\Services\TrainingLmsService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['training-lms'], false);
    $svc = new TrainingLmsService($db);
    assertTrue($svc instanceof TrainingLmsService);
};
