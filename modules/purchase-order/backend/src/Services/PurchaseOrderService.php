<?php
declare(strict_types=1);

namespace Modules\PurchaseOrder\Services;

use Core\Database\Connection;
use Modules\PurchaseOrder\Contracts\PurchaseOrderServiceInterface;

final class PurchaseOrderService implements PurchaseOrderServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function find(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function openLines(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function expectedReceipts(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function receivedSummary(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function markInvoiced(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
