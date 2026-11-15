<?php
declare(strict_types=1);

namespace Modules\ScheduleOptimizer\Services;

use Core\Database\Connection;
use Modules\ScheduleOptimizer\Contracts\ScheduleOptimizerServiceInterface;

final class ScheduleOptimizerService implements ScheduleOptimizerServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function run(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function status(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function solution(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function compare(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['schedule-optimizer.solution.ready'];
    }
}
