<?php
declare(strict_types=1);

namespace Modules\AuthRbac;

use Core\Contracts\AuthContract;
use Core\Database\Connection;
use Core\Http\Request;

final class AuthService implements AuthContract
{
    private ?array $currentUser = null;

    public function __construct(private readonly Connection $db)
    {
    }

    public function authenticate(Request $request): ?array
    {
        $header = $request->header('Authorization', '');
        if (str_starts_with((string) $header, 'Bearer ')) {
            $token = substr((string) $header, 7);
            $hash = hash('sha256', $token);
            $session = $this->db->fetchOne(
                'SELECT s.user_id, s.company_id, u.username, u.full_name, u.email
                 FROM auth_rbac__sessions s
                 JOIN auth_rbac__users u ON u.id = s.user_id
                 WHERE s.token_hash = ? AND s.expires_at > ? AND u.deleted_at IS NULL',
                [$hash, gmdate('Y-m-d H:i:s')]
            );
            if ($session !== null) {
                $this->currentUser = [
                    'id' => (int) $session['user_id'],
                    'name' => (string) $session['full_name'],
                    'email' => (string) $session['email'],
                    'company_id' => (int) $session['company_id'],
                ];
                return $this->currentUser;
            }
        }
        return null;
    }

    public function user(): ?array
    {
        return $this->currentUser;
    }

    public function hashPassword(string $password): string
    {
        $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
        return password_hash($password, $algo);
    }

    /** @return array{ok: bool, token?: string, error?: string} */
    public function login(int $companyId, string $username, string $password, string $ip = '127.0.0.1'): array
    {
        $user = $this->db->fetchOne(
            'SELECT * FROM auth_rbac__users WHERE company_id = ? AND username = ? AND deleted_at IS NULL',
            [$companyId, $username]
        );
        if ($user === null) {
            return ['ok' => false, 'error' => 'Invalid credentials'];
        }
        if (!empty($user['locked_until']) && $user['locked_until'] > gmdate('Y-m-d H:i:s')) {
            return ['ok' => false, 'error' => 'Account locked'];
        }
        if (!password_verify($password, (string) $user['password_hash'])) {
            $attempts = (int) $user['failed_attempts'] + 1;
            $lockedUntil = $attempts >= 5 ? gmdate('Y-m-d H:i:s', time() + 900) : null;
            $this->db->execute(
                'UPDATE auth_rbac__users SET failed_attempts = ?, locked_until = ?, updated_at = ? WHERE id = ?',
                [$attempts, $lockedUntil, gmdate('Y-m-d H:i:s'), $user['id']]
            );
            return ['ok' => false, 'error' => 'Invalid credentials'];
        }

        $this->db->execute(
            'UPDATE auth_rbac__users SET failed_attempts = 0, locked_until = NULL, updated_at = ? WHERE id = ?',
            [gmdate('Y-m-d H:i:s'), $user['id']]
        );

        $token = bin2hex(random_bytes(32));
        $this->db->execute(
            'INSERT INTO auth_rbac__sessions (company_id, user_id, token_hash, ip, expires_at, created_at) VALUES (?, ?, ?, ?, ?, ?)',
            [$companyId, $user['id'], hash('sha256', $token), $ip, gmdate('Y-m-d H:i:s', time() + 86400), gmdate('Y-m-d H:i:s')]
        );
        return ['ok' => true, 'token' => $token];
    }

    public function seedDefaults(int $companyId = 1): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $roles = [
            ['super-admin', 'مدیر ارشد', 'all', ['*']],
            ['admin', 'مدیر سیستم', 'all', ['auth-rbac.user.view', 'auth-rbac.user.create', 'auth-rbac.role.manage']],
            ['manager', 'مدیر بخش', 'department', ['auth-rbac.user.view']],
            ['operator', 'اپراتور', 'own', []],
            ['viewer', 'مشاهده‌گر', 'own', []],
        ];
        foreach ($roles as [$slug, $name, $scope, $perms]) {
            $exists = $this->db->fetchOne('SELECT id FROM auth_rbac__roles WHERE company_id = ? AND slug = ?', [$companyId, $slug]);
            if ($exists === null) {
                $this->db->execute(
                    'INSERT INTO auth_rbac__roles (company_id, slug, name, data_scope, permissions_json, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
                    [$companyId, $slug, $name, $scope, json_encode($perms), $now, $now]
                );
            }
        }
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['auth-rbac.user.logged_in', 'auth-rbac.user.locked', 'auth-rbac.role.updated'];
    }
}
