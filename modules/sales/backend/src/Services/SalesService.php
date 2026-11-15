<?php
declare(strict_types=1);

namespace Modules\Sales\Services;

use Core\Database\Connection;
use Modules\Sales\Contracts\SalesServiceInterface;

final class SalesService implements SalesServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function openOrders(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function backlog(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function invoiceTotals(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function find(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['sales.order.confirmed', 'sales.delivery.requested', 'sales.invoice.issued', 'sales.return.posted'];
    }
}
