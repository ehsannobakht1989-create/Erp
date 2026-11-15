<?php
declare(strict_types=1);

namespace Modules\Cmms\Services;

use Core\Database\Connection;
use Modules\Cmms\Contracts\CmmsServiceInterface;

final class CmmsService implements CmmsServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function openOrders(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function pmDue(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function downtime(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function mttrMtbf(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function costByAsset(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['cmms.workorder.created'];
    }
}
