<?php
declare(strict_types=1);

use Core\Ai\AiGateway;
use Core\Config;
use Core\Database\Connection;
use Core\Database\Migrator;
use Core\Module\ModuleRegistry;
use Core\Stubs\NullPermission;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate([], false);

    $registry = new ModuleRegistry([
        'inventory' => [
            'id' => 'inventory',
            'ai' => [
                'enabled_default' => true,
                'capabilities' => [
                    ['id' => 'assistant', 'provider' => 'avalai', 'tools' => ['inventory.search'], 'redact' => ['unit_cost']],
                    ['id' => 'forecast', 'provider' => 'off'],
                ],
            ],
        ],
    ], ['inventory']);

    // Off-mode test
    $gwOff = new AiGateway($registry, new NullPermission(true), new Config(['ai' => ['enabled' => false]]), $db);
    $resOff = $gwOff->invoke(1, 1, 'inventory', 'assistant', ['prompt' => 'hi']);
    assertEquals('disabled', $resOff['status']);

    // Permission denied test
    $gwDeny = new AiGateway($registry, new NullPermission(false), new Config(['ai' => ['enabled' => true]]), $db);
    $resDeny = $gwDeny->invoke(1, 1, 'inventory', 'assistant', ['tool' => 'inventory.search']);
    assertEquals('forbidden', $resDeny['status']);

    // Proposal creation test
    $gwAllow = new AiGateway($registry, new NullPermission(true), new Config(['ai' => ['enabled' => true]]), $db);
    $resProp = $gwAllow->invoke(1, 1, 'inventory', 'assistant', [
        'propose' => ['type' => 'transfer', 'payload' => ['qty' => 5]],
    ]);
    assertTrue($resProp['ok'] === true && isset($resProp['proposal_id']), 'Proposal should be created');
};
