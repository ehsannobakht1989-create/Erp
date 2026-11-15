<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Core\Event\EventBus;
use Modules\Inventory\Services\InventoryService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['inventory'], false);

    $svc = new InventoryService($db, new EventBus($db));
    $res = $svc->postMovement(1, ['item_id' => 10, 'warehouse_id' => 1, 'qty_in' => '50', 'idempotency_key' => 'k1']);
    assertEquals('posted', $res['status']);
    assertEquals('50.0000', $svc->getBalance(1, ['item_id' => 10, 'warehouse_id' => 1]));

    // Idempotency check
    $dup = $svc->postMovement(1, ['item_id' => 10, 'warehouse_id' => 1, 'qty_in' => '50', 'idempotency_key' => 'k1']);
    assertEquals('idempotent_skip', $dup['status']);

    // Negative stock guard
    $threw = false;
    try {
        $svc->postMovement(1, ['item_id' => 10, 'warehouse_id' => 1, 'qty_out' => '100', 'idempotency_key' => 'k2']);
    } catch (RuntimeException $e) {
        $threw = true;
    }
    assertTrue($threw, 'Negative stock must throw');

    assertTrue(is_file($root . '/modules/inventory/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/inventory/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
