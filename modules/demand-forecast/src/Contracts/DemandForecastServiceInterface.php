<?php

declare(strict_types=1);

namespace Modules\DemandForecast\Contracts;

interface DemandForecastServiceInterface
{
    public function run(int $companyId, array $params = []): mixed;

    public function results(int $companyId, array $params = []): mixed;

    public function latest(int $companyId, array $params = []): mixed;

    public function adopt(int $companyId, array $params = []): mixed;

    public function accuracy(int $companyId, array $params = []): mixed;
}
