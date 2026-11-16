<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Tenancy\AuthorizedCompanyContextResolver;
use App\Support\Tenancy\CompanyContext;
use App\Support\Tenancy\CompanyContextMiddleware;
use App\Support\Tenancy\CompanySelection;
use App\Support\Tenancy\DenyCompanyContextResolver;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

function assertContext(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$ctx = app(CompanyContext::class);
$request = Request::create('/');
$deny = new CompanyContextMiddleware($ctx, new DenyCompanyContextResolver);
foreach ([false, true] as $authenticated) {
    $request->setUserResolver(fn () => $authenticated ? (object) ['id' => 1] : null);
    $ctx->activate(99);
    try {
        $deny->handle($request, fn () => new Response('must not run'));
        throw new LogicException('Unauthorized request succeeded');
    } catch (HttpException $e) {
        assertContext($e->getStatusCode() === ($authenticated ? 403 : 401), 'Incorrect refusal status');
    }
    try {
        $ctx->companyId();
        throw new RuntimeException('Context leaked after refusal');
    } catch (LogicException $expected) {
        assertContext(str_contains($expected->getMessage(), 'required'), 'Wrong context error');
    }
}
$resolver = new class implements AuthorizedCompanyContextResolver
{
    public function resolve(Request $request): ?CompanySelection
    {
        return new CompanySelection(42, 8, 7);
    }
};
$middleware = new CompanyContextMiddleware($ctx, $resolver);
$response = $middleware->handle($request, function () use ($ctx): Response {
    assertContext($ctx->companyId() === 42 && $ctx->branchId() === 8 && $ctx->fiscalYearId() === 7, 'Authorized selection was not activated');

    return new Response('ok');
});
assertContext($response->getStatusCode() === 200, 'Successful request failed');
try {
    $middleware->handle($request, fn () => throw new RuntimeException('downstream error'));
} catch (RuntimeException $e) {
    assertContext($e->getMessage() === 'downstream error', 'Unexpected exception');
}
try {
    $ctx->companyId();
    throw new RuntimeException('Context leaked after exception');
} catch (LogicException $expected) {
    assertContext(str_contains($expected->getMessage(), 'required'), 'Wrong context error');
}
echo "Company context middleware tests passed.\n";
