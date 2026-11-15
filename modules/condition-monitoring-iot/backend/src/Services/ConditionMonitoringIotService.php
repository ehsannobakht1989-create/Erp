<?php
declare(strict_types=1);

namespace Modules\ConditionMonitoringIot\Services;

use Core\Database\Connection;
use Modules\ConditionMonitoringIot\Contracts\ConditionMonitoringIotServiceInterface;

final class ConditionMonitoringIotService implements ConditionMonitoringIotServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function latest(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function series(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function activeAlarms(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function health(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
