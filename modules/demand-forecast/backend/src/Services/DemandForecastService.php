<?php
declare(strict_types=1);

namespace Modules\DemandForecast\Services;

use Core\Database\Connection;
use Modules\DemandForecast\Contracts\DemandForecastServiceInterface;

final class DemandForecastService implements DemandForecastServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function run(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function results(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function latest(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function adopt(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function accuracy(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
