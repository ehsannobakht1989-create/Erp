<?php
declare(strict_types=1);

namespace Modules\LotTraceability\Services;

use Core\Database\Connection;
use Modules\LotTraceability\Contracts\LotTraceabilityServiceInterface;

final class LotTraceabilityService implements LotTraceabilityServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function backward(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function forward(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function recallScope(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function affectedCustomers(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function where(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
