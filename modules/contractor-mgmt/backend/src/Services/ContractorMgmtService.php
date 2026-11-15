<?php
declare(strict_types=1);

namespace Modules\ContractorMgmt\Services;

use Core\Database\Connection;
use Modules\ContractorMgmt\Contracts\ContractorMgmtServiceInterface;

final class ContractorMgmtService implements ContractorMgmtServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function isEligible(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function balance(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function retention(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['contractor-mgmt.statement.approved', 'contractor-mgmt.contractor.suspended'];
    }
}
