<?php
declare(strict_types=1);

namespace Modules\Crm\Services;

use Core\Database\Connection;
use Modules\Crm\Contracts\CrmServiceInterface;

final class CrmService implements CrmServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function customer360(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function openOpportunities(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function pipeline(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['crm.lead.created', 'crm.opportunity.won'];
    }
}
