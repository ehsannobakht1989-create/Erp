<?php

declare(strict_types=1);

namespace App\Support\Defaults;

use App\Support\Contracts\AuthContract;
use App\Support\Contracts\CompanyContract;
use App\Support\Contracts\DataScopeContract;
use App\Support\Contracts\PermissionContract;
use Illuminate\Http\Request;

/** Defaults confer no identity, company membership or permission. */
final class DenyPlatformAccess implements AuthContract, CompanyContract, DataScopeContract, PermissionContract
{
    public function authenticate(Request $request): ?array
    {
        return null;
    }

    public function user(): ?array
    {
        return null;
    }

    public function resolveCompanyId(Request $request, ?array $user): ?int
    {
        return null;
    }

    public function can(int $userId, int $companyId, string $permission): bool
    {
        return false;
    }

    public function scopeFor(int $userId, int $companyId, string $permission): array
    {
        return ['level' => 'none', 'values' => []];
    }
}
