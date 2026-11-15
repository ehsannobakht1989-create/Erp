<?php
declare(strict_types=1);

namespace Modules\WarrantyAfterSales\Services;

use Core\Database\Connection;
use Modules\WarrantyAfterSales\Contracts\WarrantyAfterSalesServiceInterface;

final class WarrantyAfterSalesService implements WarrantyAfterSalesServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function isUnderWarranty(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function claimsFor(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function failureStats(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
