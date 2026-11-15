<?php
declare(strict_types=1);

namespace Modules\Crm\Contracts;

interface CrmServiceInterface
{
    public function customer360(int $companyId, array $params = []): mixed;
    public function openOpportunities(int $companyId, array $params = []): mixed;
    public function pipeline(int $companyId, array $params = []): mixed;
}
