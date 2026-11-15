<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\DynamicFields\DynamicFieldService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['dynamic-fields'], false);

    $now = gmdate('Y-m-d H:i:s');
    $db->execute(
        'INSERT INTO dynamic_fields__fields (company_id, entity, field_key, label_fa, field_type, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
        [1, 'item-master.item', 'alloy_grade', 'گرید آلیاژ', 'string', $now, $now]
    );

    $svc = new DynamicFieldService($db);
    $fields = $svc->fieldsFor(1, 'item-master.item');
    assertEquals(1, count($fields));
    $svc->saveValues(1, 'item-master.item', 100, ['alloy_grade' => '304L']);
    $val = $db->fetchOne('SELECT val_text FROM dynamic_fields__values WHERE entity_id = ?', ['100']);
    assertEquals('304L', $val['val_text'] ?? null);
};
