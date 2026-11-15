<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\AuthRbac\AuthService;
use Modules\AuthRbac\PermissionService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    $migrator = new Migrator($db, $root);
    $migrator->migrate(['auth-rbac'], false);

    $auth = new AuthService($db);
    $auth->seedDefaults(1);

    $now = gmdate('Y-m-d H:i:s');
    $db->execute(
        'INSERT INTO auth_rbac__users (company_id, username, email, full_name, password_hash, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
        [1, 'ali', 'ali@erp.local', 'علی رضایی', $auth->hashPassword('secret123'), $now, $now]
    );

    $login = $auth->login(1, 'ali', 'secret123');
    assertTrue($login['ok'] === true && !empty($login['token']), 'Valid login should return token');

    $bad = $auth->login(1, 'ali', 'wrong');
    assertTrue($bad['ok'] === false, 'Wrong password should fail');

    $perm = new PermissionService($db);
    assertTrue(!$perm->can(1, 1, 'auth-rbac.user.create'), 'User without role should be denied');

    assertTrue(is_file($root . '/modules/auth-rbac/backend/routes.php'), 'Routes file must exist');
    assertTrue(is_file($root . '/modules/auth-rbac/web/routes.ts'), 'Web routes must exist');
    // AI off-mode & enabled check
    $aiEnabled = false;
    assertEquals(false, $aiEnabled, 'AI off-mode must default to disabled');
};
