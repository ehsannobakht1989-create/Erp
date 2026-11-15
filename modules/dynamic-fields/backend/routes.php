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
};
