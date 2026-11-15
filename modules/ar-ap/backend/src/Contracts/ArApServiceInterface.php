<?php
declare(strict_types=1);

namespace Modules\ArAp\Contracts;

interface ArApServiceInterface
{
    public function balance(int $companyId, array $params = []): mixed;
    public function openItems(int $companyId, array $params = []): mixed;
    public function overdue(int $companyId, array $params = []): mixed;
    public function creditExposure(int $companyId, array $params = []): mixed;
    public function proposePayments(int $companyId, array $params = []): mixed;
}
