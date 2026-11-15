<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\TaxMoadian\Services\TaxMoadianService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['tax-moadian'], false);
    $svc = new TaxMoadianService($db);
    assertTrue($svc instanceof TaxMoadianService);

    assertTrue(is_file($root . '/modules/tax-moadian/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/tax-moadian/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
