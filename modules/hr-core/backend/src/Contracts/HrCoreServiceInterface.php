<?php
declare(strict_types=1);

namespace Modules\HrCore\Contracts;

interface HrCoreServiceInterface
{
    public function employee(int $companyId, array $params = []): mixed;
    public function byUser(int $companyId, array $params = []): mixed;
    public function byPersonnelNo(int $companyId, array $params = []): mixed;
    public function lookup(int $companyId, array $params = []): mixed;
    public function subordinates(int $companyId, array $params = []): mixed;
    public function activeEmployees(int $companyId, array $params = []): mixed;
    public function employmentAt(int $companyId, array $params = []): mixed;
    public function isActive(int $companyId, array $params = []): mixed;
}
