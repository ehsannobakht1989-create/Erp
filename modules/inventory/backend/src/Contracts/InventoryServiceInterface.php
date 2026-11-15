<?php
declare(strict_types=1);

namespace Modules\Inventory\Contracts;

interface InventoryServiceInterface
{
    public function getBalance(int $companyId, array $params = []): mixed;
    public function getAvailable(int $companyId, array $params = []): mixed;
    public function hasStock(int $companyId, array $params = []): mixed;
    public function reserve(int $companyId, array $params = []): mixed;
    public function release(int $companyId, array $params = []): mixed;
    public function postMovement(int $companyId, array $params = []): mixed;
    public function suggestPicking(int $companyId, array $params = []): mixed;
    public function getLot(int $companyId, array $params = []): mixed;
    public function expiringLots(int $companyId, array $params = []): mixed;
    public function valuationFeed(int $companyId, array $params = []): mixed;
}
