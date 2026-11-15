<?php
declare(strict_types=1);

namespace Modules\ParkingVehicles\Contracts;

interface ParkingVehiclesServiceInterface
{
    public function availability(int $companyId, array $params = []): mixed;
    public function documentsExpiring(int $companyId, array $params = []): mixed;
    public function costPerKm(int $companyId, array $params = []): mixed;
}
