<?php

declare(strict_types=1);

namespace Modules\Costing\Contracts;

interface CostingServiceInterface
{
    public function itemCost(int $companyId, array $params = []): mixed;

    public function woCost(int $companyId, array $params = []): mixed;

    public function wipValue(int $companyId, array $params = []): mixed;

    public function margin(int $companyId, array $params = []): mixed;

    public function simulate(int $companyId, array $params = []): mixed;

    public function valuationOf(int $companyId, array $params = []): mixed;

    public function runPeriod(int $companyId, array $params = []): mixed;
}
