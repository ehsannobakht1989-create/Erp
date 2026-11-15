<?php
declare(strict_types=1);

namespace Modules\ContractorMgmt\Contracts;

interface ContractorMgmtServiceInterface
{
    public function isEligible(int $companyId, array $params = []): mixed;
    public function balance(int $companyId, array $params = []): mixed;
    public function retention(int $companyId, array $params = []): mixed;
}
