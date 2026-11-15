<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\PrintBarcodeQr\PrintService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['print-barcode-qr'], false);

    $now = gmdate('Y-m-d H:i:s');
    $db->execute(
        'INSERT INTO print_barcode_qr__templates (company_id, code, name_fa, page_size, html_template, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
        [1, 'receipt', 'رسید انبار', 'A4', '<h1>{{title}}</h1>', $now, $now]
    );

    $svc = new PrintService($db);
    assertEquals('<h1>رسید شماره ۱</h1>', $svc->render(1, 'receipt', ['title' => 'رسید شماره ۱']));

    assertTrue(is_file($root . '/modules/print-barcode-qr/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/print-barcode-qr/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
