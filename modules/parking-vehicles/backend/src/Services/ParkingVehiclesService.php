<?php
declare(strict_types=1);

namespace Modules\ParkingVehicles\Services;

use Core\Database\Connection;
use Modules\ParkingVehicles\Contracts\ParkingVehiclesServiceInterface;

final class ParkingVehiclesService implements ParkingVehiclesServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function availability(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function documentsExpiring(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function costPerKm(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
