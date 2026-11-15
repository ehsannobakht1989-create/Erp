<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\QueryBuilder;

return function (): void {
    $db = Connection::fromDsn('sqlite::memory:');
    $qb = new QueryBuilder($db, 'inventory', true);

    $threw = false;
    try {
        $qb->table('sales__orders');
    } catch (RuntimeException $e) {
        $threw = true;
    }
    assertTrue($threw, 'Accessing another module table must throw RuntimeException');
};
