<?php

declare(strict_types=1);

namespace App\Support\Contracts;

/** Row visibility for a user's permission: all, branch, department, own or none. */
interface DataScopeContract
{
    /** @return array{level: 'all'|'branch'|'department'|'own'|'none', values: list<int>} */
    public function scopeFor(int $userId, int $companyId, string $permission): array;
}
