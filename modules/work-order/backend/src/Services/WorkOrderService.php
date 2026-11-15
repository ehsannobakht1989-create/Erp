<?php
declare(strict_types=1);

namespace Modules\WorkOrder\Services;

use Core\Database\Connection;
use Modules\WorkOrder\Contracts\WorkOrderServiceInterface;

final class WorkOrderService implements WorkOrderServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function find(int $companyId, int $id): ?array
    {
        return null;
    }

    public function open(int $companyId, array $filter = []): array
    {
        return [];
    }

    public function create(int $companyId, array $dto): array
    {
        return [];
    }

    public function release(int $companyId, int $id): void
    {
    }

    public function reportOperation(int $companyId, array $dto, string $idempotencyKey): void
    {
    }

    public function issueMaterial(int $companyId, int $woId, array $lines): void
    {
    }

    public function receiveOutput(int $companyId, int $woId, array $lines): void
    {
    }

    public function shortages(int $companyId, int $woId): array
    {
        return [];
    }

    public function wipQty(int $companyId, int $itemId): string
    {
        return '0.0000';
    }

    public function openSupply(int $companyId, int $itemId, string $fromDate, string $toDate): string
    {
        return '0.0000';
    }

    public function close(int $companyId, int $id): void
    {
    }
}
