<?php

declare(strict_types=1);

namespace Modules\Treasury\Contracts;

interface TreasuryServiceInterface
{
    public function bankBalance(int $companyId, array $params = []): mixed;

    public function chequesDue(int $companyId, array $params = []): mixed;

    public function createPayment(int $companyId, array $params = []): mixed;

    public function cashPosition(int $companyId, array $params = []): mixed;

    public function cashFlowForecast(int $companyId, array $params = []): mixed;
}
