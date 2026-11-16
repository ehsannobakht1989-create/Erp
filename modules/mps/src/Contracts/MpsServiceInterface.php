<?php

declare(strict_types=1);

namespace Modules\Mps\Contracts;

interface MpsServiceInterface
{
    public function atp(int $companyId, array $params = []): mixed;

    public function plan(int $companyId, array $params = []): mixed;

    public function forecast(int $companyId, array $params = []): mixed;

    public function releasePlan(int $companyId, array $params = []): mixed;

    public function scenarioCompare(int $companyId, array $params = []): mixed;
}
