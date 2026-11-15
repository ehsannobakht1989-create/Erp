<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\FixedAssets\Services\FixedAssetsService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['fixed-assets'], false);
    $svc = new FixedAssetsService($db);
    assertTrue($svc instanceof FixedAssetsService);
};
