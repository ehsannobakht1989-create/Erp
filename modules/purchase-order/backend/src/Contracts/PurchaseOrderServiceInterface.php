<?php
declare(strict_types=1);

namespace Modules\PurchaseOrder\Contracts;

interface PurchaseOrderServiceInterface
{
    public function find(int $companyId, array $params = []): mixed;
    public function openLines(int $companyId, array $params = []): mixed;
    public function expectedReceipts(int $companyId, array $params = []): mixed;
    public function receivedSummary(int $companyId, array $params = []): mixed;
    public function markInvoiced(int $companyId, array $params = []): mixed;
}
