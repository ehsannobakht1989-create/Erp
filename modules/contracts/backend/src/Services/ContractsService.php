<?php
declare(strict_types=1);

namespace Modules\Contracts\Services;

use Core\Database\Connection;
use Modules\Contracts\Contracts\ContractsServiceInterface;

final class ContractsService implements ContractsServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function activeFor(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function consumed(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function expiring(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function terms(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['contracts.contract.activated', 'contracts.milestone.due'];
    }
}
