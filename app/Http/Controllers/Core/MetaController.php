<?php

declare(strict_types=1);

namespace App\Http\Controllers\Core;

use App\Support\Auth\PermissionRegistry;
use App\Support\Contracts\PermissionContract;
use App\Support\Http\ApiResponse;
use App\Support\Modules\ModuleCatalog;
use App\Support\Modules\ModuleManager;
use App\Support\Tenancy\CompanyContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Shell bootstrap data: only modules loaded in this process, menus filtered by the caller's abilities. */
final class MetaController
{
    public function __invoke(Request $request, ModuleCatalog $catalog, ModuleManager $modules, PermissionRegistry $registry, PermissionContract $permissions, CompanyContext $context): JsonResponse
    {
        $userId = (int) $request->user()?->getAuthIdentifier();
        $companyId = $context->companyId();
        $granted = array_values(array_filter($registry->all(), fn (string $permission) => $permissions->can($userId, $companyId, $permission)));
        $manifests = $catalog->manifests();
        $out = [];
        $menu = [];
        foreach ($modules->enabled() as $id) {
            $manifest = $manifests[$id];
            $out[] = ['id' => $id, 'name' => $manifest['name'] ?? ['fa' => $id], 'version' => $manifest['version'] ?? null, 'category' => $manifest['category'] ?? null, 'status' => $manifest['status'] ?? 'stub'];
            foreach ($manifest['menu'] ?? [] as $entry) {
                $permission = $entry['permission'] ?? null;
                if ($permission === null || in_array($permission, $granted, true)) {
                    $menu[] = ['module' => $id] + $entry;
                }
            }
        }

        return ApiResponse::ok(['company_id' => $companyId, 'modules' => $out, 'menu' => $menu, 'permissions' => $granted]);
    }
}
