<?php
declare(strict_types=1);

namespace Modules\PartyMaster\Repositories;

use Core\Contracts\AuditContract;
use Core\Database\Connection;
use Core\Database\Repository;

final class PartyRepository extends Repository
{
    public function __construct(Connection $db, AuditContract $audit)
    {
        parent::__construct($db, $audit, 'party-master', 'party_master__parties');
    }

    public function findById(int $companyId, int $id): ?array
    {
        return $this->qb()->forCompany($companyId)->whereNotDeleted()->where('id', '=', $id)->first();
    }
}
