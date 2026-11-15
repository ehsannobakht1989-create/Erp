<?php
declare(strict_types=1);

namespace Core\Stubs;

use Core\Contracts\PermissionContract;

final class NullPermission implements PermissionContract
{
    public function __construct(private readonly bool $allowAll = true)
    {
    }

    public function can(int $userId, int $companyId, string $permission): bool
    {
        return $this->allowAll;
    }
}
