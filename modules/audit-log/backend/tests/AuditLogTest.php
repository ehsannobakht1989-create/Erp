<?php
declare(strict_types=1);

use Core\Ai\AiGateway;
use Core\Config;
use Core\Database\Connection;
use Core\Database\Migrator;
use Core\Module\ModuleRegistry;
use Core\Stubs\NullPermission;
use Modules\AuditLog\AuditService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['audit-log'], false);

    $svc = new AuditService($db);
    $svc->record(1, 10, 'item-master', 'item', '42', 'create', ['code' => 'A1'], ['ip' => '127.0.0.1']);
    $svc->record(1, 10, 'item-master', 'item', '42', 'update', ['code' => 'A2'], ['ip' => '127.0.0.1']);
    $svc->record(2, 20, 'sales', 'order', '99', 'create', ['total' => 500], []);

    $rows = $svc->query(1, ['entity_type' => 'item']);
    assertEquals(2, count($rows), 'Company 1 should have 2 audit entries');

    $rowsCo2 = $svc->query(2, []);
    assertEquals(1, count($rowsCo2), 'Company 2 should have 1 audit entry');

    // AI off-mode test
    $registry = new ModuleRegistry([
        'audit-log' => [
            'id' => 'audit-log',
            'ai' => [
                'enabled_default' => false,
                'capabilities' => [
                    ['id' => 'summarize', 'provider' => 'avalai', 'tools' => ['audit-log.query'], 'redact' => ['ip']],
                ],
            ],
        ],
    ], ['audit-log']);
    $gw = new AiGateway($registry, new NullPermission(true), new Config(['ai' => ['enabled' => false]]), $db);
    $aiRes = $gw->invoke(1, 10, 'audit-log', 'summarize', ['prompt' => 'summarize']);
    assertEquals('disabled', $aiRes['status'], 'AI off-mode must return disabled');
};
