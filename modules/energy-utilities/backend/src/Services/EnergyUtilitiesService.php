<?php
declare(strict_types=1);

namespace Modules\EnergyUtilities\Services;

use Core\Database\Connection;
use Modules\EnergyUtilities\Contracts\EnergyUtilitiesServiceInterface;

final class EnergyUtilitiesService implements EnergyUtilitiesServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function consumption(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function cost(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function sec(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function allocation(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['energy-utilities.period.allocated', 'energy-utilities.target.breached'];
    }
}
