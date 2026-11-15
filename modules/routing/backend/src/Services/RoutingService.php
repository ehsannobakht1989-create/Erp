<?php
declare(strict_types=1);

namespace Modules\Routing\Services;

use Core\Database\Connection;
use Modules\Routing\Contracts\RoutingServiceInterface;

final class RoutingService implements RoutingServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function findActive(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function workCenter(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function capacity(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function standardTime(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function costRates(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
