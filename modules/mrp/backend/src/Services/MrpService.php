<?php
declare(strict_types=1);

namespace Modules\Mrp\Services;

use Core\Database\Connection;
use Modules\Mrp\Contracts\MrpServiceInterface;

final class MrpService implements MrpServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function runStatus(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function plannedOrders(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function pegging(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function firm(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function requirements(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function mrpParams(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
