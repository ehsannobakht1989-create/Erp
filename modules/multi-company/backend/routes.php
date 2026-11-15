<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Modules\MultiCompany\NumberingService;

return function (Router $router, Container $container): void {
    $router->get('/companies', fn(): Response => Response::ok([]), 'multi-company.company.view');
    $router->post('/sequences/next', function (Request $req) use ($container): Response {
        $svc = $container->make(NumberingService::class);
        $num = $svc->next(
            (int) ($req->attributes['company_id'] ?? 1),
            (string) $req->input('seq_key', 'DOC'),
            (string) $req->input('pattern', 'DOC-{YY}-{####}')
        );
        return Response::ok(['number' => $num]);
    }, 'multi-company.company.manage');
};
