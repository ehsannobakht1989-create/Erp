<?php
declare(strict_types=1);

namespace Modules\Sales\Contracts;

interface SalesServiceInterface
{
    public function openOrders(int $companyId, array $params = []): mixed;
    public function backlog(int $companyId, array $params = []): mixed;
    public function invoiceTotals(int $companyId, array $params = []): mixed;
    public function find(int $companyId, array $params = []): mixed;
}
