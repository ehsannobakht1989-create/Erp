<?php

declare(strict_types=1);

namespace Modules\WarrantyAfterSales\Contracts;

interface WarrantyAfterSalesServiceInterface
{
    public function isUnderWarranty(int $companyId, array $params = []): mixed;

    public function claimsFor(int $companyId, array $params = []): mixed;

    public function failureStats(int $companyId, array $params = []): mixed;
}
