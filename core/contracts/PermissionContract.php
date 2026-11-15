<?php
declare(strict_types=1);

namespace Core\Contracts;

interface PermissionContract
{
    public function can(int $userId, int $companyId, string $permission): bool;
}
