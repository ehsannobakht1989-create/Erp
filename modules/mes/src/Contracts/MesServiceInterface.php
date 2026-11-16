<?php

declare(strict_types=1);

namespace Modules\Mes\Contracts;

interface MesServiceInterface
{
    public function runningOps(int $companyId, array $params = []): mixed;

    public function oeeInputs(int $companyId, array $params = []): mixed;

    public function downtime(int $companyId, array $params = []): mixed;

    public function tagValue(int $companyId, array $params = []): mixed;

    public function operatorStatus(int $companyId, array $params = []): mixed;
}
