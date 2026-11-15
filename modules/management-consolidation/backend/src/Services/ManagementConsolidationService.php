<?php
declare(strict_types=1);

namespace Modules\ManagementConsolidation\Services;

use Core\Database\Connection;
use Modules\ManagementConsolidation\Contracts\ManagementConsolidationServiceInterface;

final class ManagementConsolidationService implements ManagementConsolidationServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function pnl(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function profitability(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function pack(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['management-consolidation.pack.published'];
    }
}
