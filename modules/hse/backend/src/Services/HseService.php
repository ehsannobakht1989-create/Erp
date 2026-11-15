<?php
declare(strict_types=1);

namespace Modules\Hse\Services;

use Core\Database\Connection;
use Modules\Hse\Contracts\HseServiceInterface;

final class HseService implements HseServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function openPermits(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function incidentStats(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function ppeValid(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function overdueActions(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
