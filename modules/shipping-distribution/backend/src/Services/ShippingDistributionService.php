<?php
declare(strict_types=1);

namespace Modules\ShippingDistribution\Services;

use Core\Database\Connection;
use Modules\ShippingDistribution\Contracts\ShippingDistributionServiceInterface;

final class ShippingDistributionService implements ShippingDistributionServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function shipmentFor(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function eta(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function carrierCost(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function otif(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
