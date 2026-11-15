<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Core\Event\EventBus;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate([], false);

    $bus = new EventBus($db);
    $attempts = 0;
    $bus->listen('test.item.created', function () use (&$attempts): void {
        $attempts++;
        if ($attempts === 1) {
            throw new RuntimeException('Transient failure');
        }
    }, 'l1', true);

    $bus->emit('test.item.created', ['id' => 1], 1, 1);
    $s1 = $bus->processOutbox();
    assertEquals(1, $s1['failed'], 'First attempt should fail and schedule retry');

    // Force next_attempt_at to past for immediate retry test
    $db->execute("UPDATE core__outbox SET next_attempt_at = '2000-01-01 00:00:00'");
    $s2 = $bus->processOutbox();
    assertEquals(1, $s2['processed'], 'Second attempt should succeed');
};
