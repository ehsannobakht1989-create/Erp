<?php
declare(strict_types=1);

namespace Modules\Mps\Services;

use Core\Database\Connection;
use Modules\Mps\Contracts\MpsServiceInterface;

final class MpsService implements MpsServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function atp(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function plan(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function forecast(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function releasePlan(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function scenarioCompare(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
