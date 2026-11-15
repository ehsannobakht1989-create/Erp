<?php
declare(strict_types=1);

namespace Modules\ManagementConsolidation\Contracts;

interface ManagementConsolidationServiceInterface
{
    public function pnl(int $companyId, array $params = []): mixed;
    public function profitability(int $companyId, array $params = []): mixed;
    public function pack(int $companyId, array $params = []): mixed;
}
