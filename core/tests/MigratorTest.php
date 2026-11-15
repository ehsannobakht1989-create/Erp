<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    $migrator = new Migrator($db, $root);

    $dry = $migrator->migrate([], true);
    assertTrue(count($dry) >= 1 && $dry[0]['status'] === 'pending', 'Dry run should report pending');

    $applied = $migrator->migrate([], false);
    assertTrue(count($applied) >= 1 && $applied[0]['status'] === 'applied', 'Migrate should apply core tables');

    $again = $migrator->migrate([], false);
    assertEquals('skipped', $again[0]['status'], 'Re-running migrate should skip');
};
