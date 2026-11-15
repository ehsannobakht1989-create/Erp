<?php
declare(strict_types=1);

namespace Modules\Budgeting\Services;

use Core\Database\Connection;
use Modules\Budgeting\Contracts\BudgetingServiceInterface;

final class BudgetingService implements BudgetingServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function check(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function commit(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function release(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function variance(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['budgeting.budget.approved', 'budgeting.check.exceeded'];
    }
}
