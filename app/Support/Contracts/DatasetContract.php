<?php

declare(strict_types=1);

namespace App\Support\Contracts;

interface DatasetContract
{
    /** @param array<string, mixed> $query
     * @return array{rows:list<array<string, mixed>>, meta:array<string, mixed>}
     *                                                                           Read-only execution must apply the user's permissions and data scope before fetching.
     */
    public function query(int $companyId, int $userId, string $dataset, array $query): array;
}
