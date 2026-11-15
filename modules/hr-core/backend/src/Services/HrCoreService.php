<?php
declare(strict_types=1);

namespace Modules\HrCore\Services;

use Core\Database\Connection;
use Modules\HrCore\Contracts\HrCoreServiceInterface;

final class HrCoreService implements HrCoreServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function employee(int $companyId, array $params = []): mixed
    {
        return $this->db->fetchOne('SELECT * FROM hr_core__employees WHERE company_id = ? AND id = ?', [$companyId, (int) ($params['id'] ?? 0)]);
    }

    public function byUser(int $companyId, array $params = []): mixed { return null; }

    public function byPersonnelNo(int $companyId, array $params = []): mixed
    {
        return $this->db->fetchOne('SELECT * FROM hr_core__employees WHERE company_id = ? AND personnel_no = ?', [$companyId, (string) ($params['personnel_no'] ?? '')]);
    }

    public function lookup(int $companyId, array $params = []): mixed
    {
        return $this->db->fetchAll('SELECT id, personnel_no, first_name, last_name FROM hr_core__employees WHERE company_id = ? AND status = ? LIMIT 20', [$companyId, 'active']);
    }

    public function subordinates(int $companyId, array $params = []): mixed { return []; }
    public function activeEmployees(int $companyId, array $params = []): mixed { return $this->lookup($companyId, $params); }
    public function employmentAt(int $companyId, array $params = []): mixed { return null; }

    public function isActive(int $companyId, array $params = []): mixed
    {
        $e = $this->employee($companyId, $params);
        return is_array($e) && ($e['status'] ?? '') === 'active';
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['hr-core.employee.hired', 'hr-core.employee.transferred', 'hr-core.employee.terminated'];
    }
}
