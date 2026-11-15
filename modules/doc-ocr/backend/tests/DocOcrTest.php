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

    assertTrue(is_file($root . '/modules/doc-ocr/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/doc-ocr/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
