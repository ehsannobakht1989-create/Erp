<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Pricing\Services\PricingService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['pricing'], false);
    $now = gmdate('Y-m-d H:i:s');
    $db->execute('INSERT INTO pricing__lists (company_id, code, created_at, updated_at) VALUES (1, "STD", ?, ?)', [$now, $now]);
    $db->execute('INSERT INTO pricing__lines (company_id, list_id, item_id, min_qty, price, created_at) VALUES (1, 1, 5, 1, "150000.0000", ?)', [$now]);
    $svc = new PricingService($db);
    $res = $svc->resolve(1, ['item_id' => 5, 'qty' => '2']);
    assertEquals('150000.0000', (string) $res['price']);
};
