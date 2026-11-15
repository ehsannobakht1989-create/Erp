<?php
declare(strict_types=1);

namespace Modules\Costing\Services;

use Core\Database\Connection;
use Modules\Costing\Contracts\CostingServiceInterface;

final class CostingService implements CostingServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function itemCost(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function woCost(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function wipValue(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function margin(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function simulate(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function valuationOf(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function runPeriod(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['costing.cost.updated', 'costing.wo.costed', 'costing.run.completed', 'costing.variance.recorded'];
    }
}
