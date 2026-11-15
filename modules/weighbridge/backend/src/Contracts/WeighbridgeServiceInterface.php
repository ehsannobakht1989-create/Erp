<?php
declare(strict_types=1);

namespace Modules\Weighbridge\Contracts;

interface WeighbridgeServiceInterface
{
    public function ticket(int $companyId, array $params = []): mixed;
    public function netFor(int $companyId, array $params = []): mixed;
    public function pendingTickets(int $companyId, array $params = []): mixed;
}
