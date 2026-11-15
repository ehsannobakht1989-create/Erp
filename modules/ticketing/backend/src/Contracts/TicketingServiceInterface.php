<?php
declare(strict_types=1);

namespace Modules\Ticketing\Contracts;

interface TicketingServiceInterface
{
    public function openFor(int $companyId, array $params = []): mixed;
    public function slaStats(int $companyId, array $params = []): mixed;
}
