<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\Ticketing\Services\TicketingService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['ticketing'], false);
    $svc = new TicketingService($db);
    assertTrue($svc instanceof TicketingService);
};
