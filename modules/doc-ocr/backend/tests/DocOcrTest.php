<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\DocOcr\Services\DocOcrService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['doc-ocr'], false);
    $svc = new DocOcrService($db);
    assertTrue($svc instanceof DocOcrService);
};
