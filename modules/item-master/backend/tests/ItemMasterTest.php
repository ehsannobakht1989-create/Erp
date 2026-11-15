<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Core\Event\EventBus;
use Core\Stubs\NullAudit;
use Core\Stubs\NullWorkflow;
use Modules\ItemMaster\Services\ItemMasterService;
use Modules\ItemMaster\Services\TextNormalizer;

return function (string $root): void {
    // 1. Persian TextNormalizer
    assertEquals('پیچ 8 میل گالوانیزه', TextNormalizer::normalize('پيچ ۸‌ميل گالوانيزه'));

    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['item-master'], false);

    $svc = new ItemMasterService($db, new NullAudit(), new EventBus($db), new NullWorkflow());

    // 2. Create item
    $res = $svc->create(1, 1, [
        'code' => 'STL-001',
        'name_fa' => 'ورق فولادی ۲ میل',
        'item_type' => 'raw_material',
        'base_uom_id' => 'KG',
    ]);
    assertTrue($res['ok'] === true, 'Item should be created');
    $id = (int) $res['item']['id'];

    // 3. Duplicate warning with Persian variants
    $dup = $svc->create(1, 1, [
        'code' => 'STL-002',
        'name_fa' => 'ورق فولادي 2 ميل',
        'item_type' => 'raw_material',
        'base_uom_id' => 'KG',
    ]);
    assertTrue($dup['ok'] === false && $dup['warning'] === 'DUPLICATE_NAME', 'Duplicate Persian variant should warn');

    // 4. Activate via workflow
    $act = $svc->activate(1, 1, $id);
    assertEquals('active', $act['status']);

    // 5. Immutability after use
    $svc->markUsed(1, $id, 'inventory');
    $threw = false;
    try {
        $svc->update(1, 1, $id, ['code' => 'NEW-CODE']);
    } catch (RuntimeException $e) {
        $threw = true;
    }
    assertTrue($threw, 'Code must be immutable after use');

    // 6. UoM conversion
    $now = gmdate('Y-m-d H:i:s');
    $db->execute(
        'INSERT INTO item_master__uom_conversions (company_id, item_id, from_uom, to_uom, factor, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
        [1, $id, 'ROLL', 'KG', '12.500000', $now, $now]
    );
    assertEquals('25.0000', $svc->convert(1, $id, '2', 'ROLL', 'KG'));
};
