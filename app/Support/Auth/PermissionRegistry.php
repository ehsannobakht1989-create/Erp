<?php

declare(strict_types=1);

namespace App\Support\Auth;

use App\Support\Contracts\PermissionContract;
use App\Support\Modules\ModuleCatalog;
use App\Support\Tenancy\CompanyContext;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use LogicException;

final readonly class PermissionRegistry
{
    /** @var list<string> */
    private array $permissions;

    /** @param list<string> $enabled */
    public function __construct(ModuleCatalog $catalog, array $enabled)
    {
        $permissions = [];
        foreach ($catalog->ordered($enabled) as $id) {
            foreach ($catalog->manifests()[$id]['permissions'] ?? [] as $permission) {
                $permissions[$permission] = true;
            }
        }
        $this->permissions = array_keys($permissions);
    }

    /** @return list<string> */
    public function all(): array
    {
        return $this->permissions;
    }

    public function register(Gate $gate): void
    {
        foreach ($this->permissions as $permission) {
            $gate->define($permission, function (Authenticatable $user) use ($permission): bool {
                $userId = $user->getAuthIdentifier();
                if (! ctype_digit((string) $userId) || (int) $userId < 1) {
                    return false;
                }
                try {
                    $companyId = app(CompanyContext::class)->companyId();
                } catch (LogicException) {
                    return false;
                }

                return app(PermissionContract::class)->can((int) $userId, $companyId, $permission);
            });
        }
    }
}
