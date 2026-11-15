<?php
declare(strict_types=1);

namespace Modules\Budgeting\Contracts;

interface BudgetingServiceInterface
{
    public function check(int $companyId, array $params = []): mixed;
    public function commit(int $companyId, array $params = []): mixed;
    public function release(int $companyId, array $params = []): mixed;
    public function variance(int $companyId, array $params = []): mixed;
}
