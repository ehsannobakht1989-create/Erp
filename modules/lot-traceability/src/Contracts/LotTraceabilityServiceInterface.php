<?php

declare(strict_types=1);

namespace Modules\LotTraceability\Contracts;

interface LotTraceabilityServiceInterface
{
    public function backward(int $companyId, array $params = []): mixed;

    public function forward(int $companyId, array $params = []): mixed;

    public function recallScope(int $companyId, array $params = []): mixed;

    public function affectedCustomers(int $companyId, array $params = []): mixed;

    public function where(int $companyId, array $params = []): mixed;
}
