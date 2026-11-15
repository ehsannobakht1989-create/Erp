<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Core\Event\EventBus;
use Core\Stubs\NullAudit;
use Modules\PartyMaster\Services\PartyMasterService;
use Modules\PartyMaster\Services\PartyValidator;

return function (string $root): void {
    assertTrue(PartyValidator::isValidNationalCode('0499370899'), 'Valid Iranian national code');
    assertTrue(!PartyValidator::isValidNationalCode('1111111111'), 'Repeated digits invalid');
    assertTrue(PartyValidator::isValidIban('IR820540102680020817909002'), 'Valid Iranian IBAN');

    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['party-master'], false);

    $svc = new PartyMasterService($db, new NullAudit(), new EventBus($db));
    $party = $svc->create(1, 1, ['code' => 'CUS-01', 'name_fa' => 'فولاد مبارکه', 'party_type' => 'customer']);
    assertEquals('active', $party['status']);
};
