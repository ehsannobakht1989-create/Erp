<?php
declare(strict_types=1);

namespace Modules\ShippingDistribution\Contracts;

interface ShippingDistributionServiceInterface
{
    public function shipmentFor(int $companyId, array $params = []): mixed;
    public function eta(int $companyId, array $params = []): mixed;
    public function carrierCost(int $companyId, array $params = []): mixed;
    public function otif(int $companyId, array $params = []): mixed;
}
