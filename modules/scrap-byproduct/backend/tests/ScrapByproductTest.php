<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ScrapByproduct\Services\ScrapByproductService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['scrap-byproduct'], false);
    $svc = new ScrapByproductService($db);
    assertTrue($svc instanceof ScrapByproductService);
};
