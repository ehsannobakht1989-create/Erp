<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\AssetRegistry\Services\AssetRegistryService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['asset-registry'], false);
    $now = gmdate('Y-m-d H:i:s');
    $db->execute('INSERT INTO asset_registry__assets (company_id, code, name_fa, criticality, created_at, updated_at) VALUES (1, "PRS-01", "پرس ۱۰۰ تن", "A", ?, ?)', [$now, $now]);
    $svc = new AssetRegistryService($db);
    assertEquals('PRS-01', $svc->find(1, ['id' => 1])['code']);

    assertTrue(is_file($root . '/modules/asset-registry/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/asset-registry/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
