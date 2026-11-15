<?php
declare(strict_types=1);

namespace Modules\ItemMaster\Repositories;

use Core\Contracts\AuditContract;
use Core\Database\Connection;
use Core\Database\Repository;

final class ItemRepository extends Repository
{
    public function __construct(Connection $db, AuditContract $audit)
    {
        parent::__construct($db, $audit, 'item-master', 'item_master__items');
    }

    public function findById(int $companyId, int $id): ?array
    {
        return $this->qb()->forCompany($companyId)->whereNotDeleted()->where('id', '=', $id)->first();
    }

    public function findByCode(int $companyId, string $code): ?array
    {
        return $this->qb()->forCompany($companyId)->whereNotDeleted()->where('code', '=', $code)->first();
    }
}
