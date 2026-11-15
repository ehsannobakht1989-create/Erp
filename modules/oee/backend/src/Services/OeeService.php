<?php
declare(strict_types=1);

namespace Modules\Oee\Services;

use Core\Database\Connection;
use Modules\Oee\Contracts\OeeServiceInterface;

final class OeeService implements OeeServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function oee(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function losses(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function trend(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function benchmark(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['oee-downtime.snapshot.computed', 'oee-downtime.alert.raised'];
    }
}
