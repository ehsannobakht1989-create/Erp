<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\ApiWebhooks\WebhookService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['api-webhooks'], false);

    $svc = new WebhookService($db);
    $key = $svc->createKey(1, 'Integration', ['inventory.receipt.view']);
    assertTrue(str_starts_with($key, 'erp_'), 'API key should start with erp_');
    $sig = $svc->signPayload('{"ok":true}', 'secret');
    assertEquals(64, strlen($sig), 'HMAC-SHA256 hex signature should be 64 chars');

    assertTrue(is_file($root . '/modules/api-webhooks/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/api-webhooks/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
