<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Contracts;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

function contractCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
function unavailable(callable $call): void
{
    try {
        $call();
    } catch (LogicException $e) {
        contractCheck(str_contains($e->getMessage(), 'unavailable'), 'Missing provider must be explicit');

        return;
    }
    throw new RuntimeException('Missing engine pretended to succeed');
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
foreach (glob(dirname(__DIR__, 2).'/app/Support/Contracts/*Contract.php') ?: [] as $path) {
    $class = 'App\\Support\\Contracts\\'.basename($path, '.php');
    contractCheck(app($class) instanceof $class, 'Contract must resolve independently: '.$class);
}
contractCheck(app(Contracts\AuthContract::class)->authenticate(Request::create('/')) === null, 'Default auth must not authenticate');
contractCheck(app(Contracts\AuthContract::class)->user() === null, 'Default user must be absent');
contractCheck(! app(Contracts\PermissionContract::class)->can(1, 1, 'inventory.receipt.post'), 'Default permission must deny');
contractCheck(app(Contracts\CompanyContract::class)->resolveCompanyId(Request::create('/?company_id=1'), ['id' => 1]) === null, 'Default company resolver must deny selection');
contractCheck(app(Contracts\DynamicFieldContract::class)->fieldsFor(1, 'item') === [], 'Absent field provider must supply no fields');
$ai = app(Contracts\AiGatewayContract::class)->invoke(1, 1, 'inventory', 'assistant', ['secret' => 'sensitive']);
contractCheck($ai['status'] === 'disabled' && $ai['ok'] === false && $ai['output'] === null, 'AI must be cleanly disabled');
contractCheck(! str_contains(json_encode($ai, JSON_THROW_ON_ERROR), 'sensitive'), 'Off result must not echo input');
unavailable(fn () => app(Contracts\WorkflowContract::class)->transition(1, 'approval', 1, 'approve'));
unavailable(fn () => app(Contracts\AuditContract::class)->record(1, 1, 'inventory', 'inventory_receipts', 'update', 1));
unavailable(fn () => app(Contracts\NotifierContract::class)->notify(1, 1, 'email', 'receipt'));
unavailable(fn () => app(Contracts\PrintContract::class)->render(1, 'receipt', []));
try {
    app(Contracts\NumberingContract::class)->next(1, 'inventory.receipt');
    throw new RuntimeException('Numbering without company context must fail');
} catch (LogicException $e) {
    contractCheck(stripos($e->getMessage(), 'company') !== false, 'Numbering must require active company context');
}
try {
    app(Contracts\DatasetContract::class)->query(1, 1, 'inventory.stock', []);
    throw new RuntimeException('Dataset query without company context must fail');
} catch (LogicException $e) {
    contractCheck(stripos($e->getMessage(), 'company') !== false, 'Datasets must require active company context');
}
contractCheck(app(Contracts\DataScopeContract::class)->scopeFor(1, 1, 'inventory.stock.view')['level'] === 'none', 'Default data scope must expose no rows');
$app->instance(Contracts\PermissionContract::class, new class implements Contracts\PermissionContract
{
    public function can(int $userId, int $companyId, string $permission): bool
    {
        return $userId === 1 && $companyId === 2 && $permission === 'fixture.record.view';
    }
});
contractCheck(app(Contracts\PermissionContract::class)->can(1, 2, 'fixture.record.view'), 'Module must be able to replace fallback binding');
echo "Independent platform contract tests passed.\n";
