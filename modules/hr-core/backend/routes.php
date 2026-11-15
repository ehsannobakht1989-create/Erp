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

    $router->post('/actions/create-0', fn(): Response => Response::ok(['permission' => 'hr-core.employee.create']), 'hr-core.employee.create');
    $router->post('/actions/update-1', fn(): Response => Response::ok(['permission' => 'hr-core.employee.update']), 'hr-core.employee.update');
    $router->post('/actions/terminate-2', fn(): Response => Response::ok(['permission' => 'hr-core.employee.terminate']), 'hr-core.employee.terminate');
    $router->post('/actions/view-3', fn(): Response => Response::ok(['permission' => 'hr-core.sensitive.view']), 'hr-core.sensitive.view');
};
