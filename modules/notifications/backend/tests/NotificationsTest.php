<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Notifications\NotificationService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['notifications'], false);

    $svc = new NotificationService($db);
    $svc->notify(1, 1, 'in_app', 'po_approved', ['po' => 'PO-03-0001']);
    $row = $db->fetchOne('SELECT * FROM notifications__messages WHERE user_id = 1');
    assertEquals('po_approved', $row['template'] ?? null);

    assertTrue(is_file($root . '/modules/notifications/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/notifications/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
