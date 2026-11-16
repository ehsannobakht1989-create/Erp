<?php

declare(strict_types=1);

namespace Modules\Tooling\Contracts;

interface ToolingServiceInterface
{
    public function availability(int $companyId, array $params = []): mixed;

    public function lifeLeft(int $companyId, array $params = []): mixed;

    public function required(int $companyId, array $params = []): mixed;

    public function amortizationPerUnit(int $companyId, array $params = []): mixed;
}
