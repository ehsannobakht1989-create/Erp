<?php
declare(strict_types=1);

namespace Modules\OrgStructure\Services;

use Core\Database\Connection;
use Modules\OrgStructure\Contracts\OrgStructureServiceInterface;

final class OrgStructureService implements OrgStructureServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function unitsTree(int $companyId, array $params = []): mixed
    {
        return $this->db->fetchAll('SELECT * FROM org_structure__units WHERE company_id = ? ORDER BY id ASC', [$companyId]);
    }

    public function unit(int $companyId, array $params = []): mixed
    {
        $id = (int) ($params['id'] ?? 0);
        return $this->db->fetchOne('SELECT * FROM org_structure__units WHERE company_id = ? AND id = ?', [$companyId, $id]);
    }

    public function managerOf(int $companyId, array $params = []): mixed { return null; }
    public function approverChain(int $companyId, array $params = []): mixed { return []; }

    public function costCenter(int $companyId, array $params = []): mixed
    {
        $id = (int) ($params['id'] ?? 0);
        return $this->db->fetchOne('SELECT * FROM org_structure__cost_centers WHERE company_id = ? AND id = ?', [$companyId, $id]);
    }

    public function costCenters(int $companyId, array $params = []): mixed
    {
        return $this->db->fetchAll('SELECT * FROM org_structure__cost_centers WHERE company_id = ? ORDER BY id ASC', [$companyId]);
    }

    public function isWorkday(int $companyId, array $params = []): mixed
    {
        $date = (string) ($params['date'] ?? gmdate('Y-m-d'));
        return date('N', strtotime($date)) !== '5'; // Friday weekend
    }

    public function workingMinutes(int $companyId, array $params = []): mixed { return 480; }
    public function shift(int $companyId, array $params = []): mixed { return null; }
    public function shiftsFor(int $companyId, array $params = []): mixed { return []; }
}
