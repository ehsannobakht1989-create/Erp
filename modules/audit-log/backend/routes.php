<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Modules\AuditLog\AuditService;

return function (Router $router, Container $container): void {
    $router->get('/entries', function (Request $req) use ($container): Response {
        $svc = $container->make(AuditService::class);
        $companyId = (int) ($req->header('X-Company-Id') ?? 1);
        return Response::ok($svc->query($companyId, $req->query()));
    }, 'audit-log.entry.view');

    $router->get('/entries/{id}', function (Request $req, array $params) use ($container): Response {
        $svc = $container->make(AuditService::class);
        $companyId = (int) ($req->header('X-Company-Id') ?? 1);
        $rows = $svc->query($companyId, ['id' => (int) ($params['id'] ?? 0)]);
        return Response::ok($rows[0] ?? null);
    }, 'audit-log.entry.view');

    $router->get('/entries/export', function (Request $req) use ($container): Response {
        $svc = $container->make(AuditService::class);
        $companyId = (int) ($req->header('X-Company-Id') ?? 1);
        return Response::ok(['format' => 'json', 'rows' => $svc->query($companyId, $req->query())]);
    }, 'audit-log.entry.export');

    $router->post('/ai/summarize', function (Request $req) use ($container): Response {
        $svc = $container->make(AuditService::class);
        $companyId = (int) ($req->header('X-Company-Id') ?? 1);
        return Response::ok(['summary' => 'audit_summary', 'count' => count($svc->query($companyId, []))]);
    }, 'audit-log.ai.use');
};
