<?php

declare(strict_types=1);

namespace Modules\WorkOrder\Contracts;

interface WorkOrderServiceInterface
{
    public function find(int $companyId, int $id): ?array;

    /** @param array<string, mixed> $filter */
    public function open(int $companyId, array $filter = []): array;

    /** @param array<string, mixed> $dto */
    public function create(int $companyId, array $dto): array;

    public function release(int $companyId, int $id): void;

    /** @param array<string, mixed> $dto */
    public function reportOperation(int $companyId, array $dto, string $idempotencyKey): void;

    /** @param array<int, array<string, mixed>> $lines */
    public function issueMaterial(int $companyId, int $woId, array $lines): void;

    /** @param array<int, array<string, mixed>> $lines */
    public function receiveOutput(int $companyId, int $woId, array $lines): void;

    public function shortages(int $companyId, int $woId): array;

    public function wipQty(int $companyId, int $itemId): string;

    public function openSupply(int $companyId, int $itemId, string $fromDate, string $toDate): string;

    public function close(int $companyId, int $id): void;
}
