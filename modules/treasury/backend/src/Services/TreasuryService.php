<?php
declare(strict_types=1);

namespace Modules\Treasury\Services;

use Core\Database\Connection;
use Modules\Treasury\Contracts\TreasuryServiceInterface;

final class TreasuryService implements TreasuryServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function bankBalance(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function chequesDue(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function createPayment(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function cashPosition(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function cashFlowForecast(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['treasury.cheque.status_changed'];
    }
}
