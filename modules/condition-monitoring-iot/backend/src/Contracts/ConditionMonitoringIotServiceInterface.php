<?php
declare(strict_types=1);

namespace Modules\ConditionMonitoringIot\Contracts;

interface ConditionMonitoringIotServiceInterface
{
    public function latest(int $companyId, array $params = []): mixed;
    public function series(int $companyId, array $params = []): mixed;
    public function activeAlarms(int $companyId, array $params = []): mixed;
    public function health(int $companyId, array $params = []): mixed;
}
