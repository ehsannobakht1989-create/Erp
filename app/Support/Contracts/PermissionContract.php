<?php

declare(strict_types=1);

namespace App\Support\Contracts;

interface PermissionContract
{
    public function can(int $userId, int $companyId, string $permission): bool;
}
