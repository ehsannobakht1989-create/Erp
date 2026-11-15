<?php
declare(strict_types=1);

namespace Modules\ItemMaster\Contracts;

interface ItemMasterServiceInterface
{
    public function find(int $companyId, int $id): ?array;

    public function findByCode(int $companyId, string $code): ?array;

    /** @param array<string, mixed> $filters */
    public function search(int $companyId, string $query = '', array $filters = [], int $limit = 25): array;

    public function lookup(int $companyId, string $query = '', int $limit = 20): array;

    public function isActive(int $companyId, int $id): bool;

    public function convert(int $companyId, int $itemId, string $qty, string $fromUom, string $toUom, int $scale = 4): string;

    public function markUsed(int $companyId, int $id, string $byModule): void;
}
