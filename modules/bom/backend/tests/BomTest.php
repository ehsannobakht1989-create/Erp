<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Bom\Services\BomService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['bom'], false);
    $now = gmdate('Y-m-d H:i:s');
    $db->execute('INSERT INTO bom__boms (company_id, item_id, code, revision, status, created_at) VALUES (1, 100, "BOM-100", 1, "approved", ?)', [$now]);
    $db->execute('INSERT INTO bom__lines (company_id, bom_id, component_item_id, qty_per) VALUES (1, 1, 200, "2.5000")');

    $svc = new BomService($db);
    $res = $svc->explode(1, ['item_id' => 100, 'qty' => '4']);
    assertEquals('10.0000', $res[0]['qty']);

    $threw = false;
    try {
        $svc->explode(1, ['item_id' => 100, 'qty' => '1', 'visited' => [100]]);
    } catch (RuntimeException $e) {
        $threw = true;
    }
    assertTrue($threw, 'BOM cycle must throw');
};
