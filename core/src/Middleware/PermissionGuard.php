<?php
declare(strict_types=1);

namespace Core\Middleware;

use Core\Contracts\PermissionContract;
use Core\Http\Request;
use Core\Http\Response;

final class PermissionGuard
{
    public function __construct(
        private readonly PermissionContract $permissions,
        private readonly string $requiredPermission,
    ) {
    }

    public function handle(Request $request, callable $next): Response
    {
        $user = $request->attributes['user'] ?? null;
        $companyId = (int) ($request->attributes['company_id'] ?? 0);
        if (!is_array($user) || !isset($user['id'])) {
            return Response::error('UNAUTHORIZED', 'Authentication required', 401);
        }
        if (!$this->permissions->can((int) $user['id'], $companyId, $this->requiredPermission)) {
            return Response::error('FORBIDDEN', 'Insufficient permissions', 403, [
                'permission' => $this->requiredPermission,
            ]);
        }
        return $next($request);
    }
}
