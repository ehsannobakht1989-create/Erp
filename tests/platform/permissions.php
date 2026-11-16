<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Auth\PermissionRegistry;
use App\Support\Contracts\PermissionContract;
use App\Support\Modules\ModuleCatalog;
use App\Support\Tenancy\CompanyContext;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Console\Kernel;

function permissionCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$catalog = app(ModuleCatalog::class);
permissionCheck(app(PermissionRegistry::class)->all() === [], 'Disabled modules must contribute no permissions');
$registry = new PermissionRegistry($catalog, ['audit-log']);
permissionCheck($registry->all() === $catalog->manifests()['audit-log']['permissions'], 'Registry must collect enabled declarations only');
$gate = app(Gate::class);
$registry->register($gate);
$permission = $registry->all()[0];
$user = new GenericUser(['id' => 1]);
$forUser = $gate->forUser($user);
permissionCheck(! $forUser->allows($permission), 'No company context must deny');
app(CompanyContext::class)->activate(1);
permissionCheck(! $forUser->allows($permission), 'Default permission service must deny');
$app->instance(PermissionContract::class, new class implements PermissionContract
{
    public function can(int $userId, int $companyId, string $permission): bool
    {
        return $userId === 1 && $companyId === 1 && str_starts_with($permission, 'audit-log.');
    }
});
permissionCheck($forUser->allows($permission), 'Authorized enabled permission must pass');
permissionCheck(! $forUser->allows('unknown.record.view'), 'Undeclared permission must deny');
permissionCheck(! $gate->forUser(new GenericUser(['id' => 2]))->allows($permission), 'Different user must not inherit grant');
permissionCheck(! $gate->forUser(new GenericUser(['id' => 'not-an-integer']))->allows($permission), 'Invalid actor ID must deny');
app(CompanyContext::class)->activate(2);
permissionCheck(! $forUser->allows($permission), 'Company-specific grant must not leak to another company');
app(CompanyContext::class)->clear();
permissionCheck(! $forUser->allows($permission), 'Cleared context must deny');
echo "Manifest permission Gate tests passed.\n";
