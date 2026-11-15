<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Modules\HrCore\Services\HrCoreService;

return function (Router $router, Container $container): void {
    $router->get('/employees', fn(Request $req): Response => Response::ok(
        $container->make(HrCoreService::class)->activeEmployees((int) ($req->attributes['company_id'] ?? 1))
    ), 'hr-core.employee.view');
};
