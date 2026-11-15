<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Core\Event\EventBus;
use Modules\AccountingGl\Services\AccountingGlService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['accounting-gl'], false);

    $svc = new AccountingGlService($db, new EventBus($db));
    $res = $svc->requestJournal(1, [
        'journal_no' => 'J-1403-0001',
        'lines' => [
            ['account_id' => 1, 'debit' => '1000.0000', 'credit' => '0'],
            ['account_id' => 2, 'debit' => '0', 'credit' => '1000.0000'],
        ],
    ]);
    assertEquals('permanent', $res['status']);

    $threw = false;
    try {
        $svc->requestJournal(1, ['lines' => [['account_id' => 1, 'debit' => '100', 'credit' => '50']]]);
    } catch (RuntimeException $e) {
        $threw = true;
    }
    assertTrue($threw, 'Unbalanced journal must throw');
};
