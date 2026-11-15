<?php
declare(strict_types=1);

namespace Modules\Ticketing\Services;

use Core\Database\Connection;
use Modules\Ticketing\Contracts\TicketingServiceInterface;

final class TicketingService implements TicketingServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function openFor(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function slaStats(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['ticketing.ticket.created'];
    }
}
