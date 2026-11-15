<?php
declare(strict_types=1);

namespace Modules\AuthRbac;

use Core\Contracts\PermissionContract;
use Core\Database\Connection;

final class PermissionService implements PermissionContract, ScopeContract
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function can(int $userId, int $companyId, string $permission): bool
    {
        $rows = $this->db->fetchAll(
            'SELECT r.permissions_json
             FROM auth_rbac__user_roles ur
             JOIN auth_rbac__roles r ON r.id = ur.role_id
             WHERE ur.user_id = ? AND ur.company_id = ?',
            [$userId, $companyId]
        );
        foreach ($rows as $row) {
            $perms = json_decode((string) $row['permissions_json'], true) ?: [];
            if (in_array('*', $perms, true) || in_array($permission, $perms, true)) {
                return true;
            }
        }
        return false;
    }

    public function scopeFor(int $userId, int $companyId, string $module): string
    {
        $rows = $this->db->fetchAll(
            'SELECT r.data_scope
             FROM auth_rbac__user_roles ur
             JOIN auth_rbac__roles r ON r.id = ur.role_id
             WHERE ur.user_id = ? AND ur.company_id = ?',
            [$userId, $companyId]
        );
        $order = ['own' => 1, 'department' => 2, 'branch' => 3, 'all' => 4];
        $best = 'own';
        foreach ($rows as $row) {
            $s = (string) ($row['data_scope'] ?? 'own');
            if (($order[$s] ?? 0) > ($order[$best] ?? 0)) {
                $best = $s;
            }
        }
        return $best;
    }
}
