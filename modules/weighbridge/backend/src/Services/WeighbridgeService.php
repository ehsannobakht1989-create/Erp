<?php
declare(strict_types=1);

namespace Modules\Weighbridge\Services;

use Core\Database\Connection;
use Modules\Weighbridge\Contracts\WeighbridgeServiceInterface;

final class WeighbridgeService implements WeighbridgeServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function ticket(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function netFor(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function pendingTickets(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['weighbridge.ticket.completed', 'weighbridge.ticket.voided'];
    }
}
