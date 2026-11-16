<?php

declare(strict_types=1);

namespace Modules\ScheduleOptimizer\Contracts;

interface ScheduleOptimizerServiceInterface
{
    public function run(int $companyId, array $params = []): mixed;

    public function status(int $companyId, array $params = []): mixed;

    public function solution(int $companyId, array $params = []): mixed;

    public function compare(int $companyId, array $params = []): mixed;
}
