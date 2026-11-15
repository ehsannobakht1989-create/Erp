<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Modules\DynamicFields\DynamicFieldService;

return function (Router $router, Container $container): void {
    $router->get('/fields', function (Request $req) use ($container): Response {
        $svc = $container->make(DynamicFieldService::class);
        $fields = $svc->fieldsFor((int) ($req->attributes['company_id'] ?? 1), (string) $req->input('entity', ''));
        return Response::ok($fields);
    }, 'dynamic-fields.field.view');

    $router->post('/actions/manage-0', fn(): Response => Response::ok(['permission' => 'dynamic-fields.field.manage']), 'dynamic-fields.field.manage');
    $router->post('/actions/use-1', fn(): Response => Response::ok(['permission' => 'dynamic-fields.ai.use']), 'dynamic-fields.ai.use');
};
