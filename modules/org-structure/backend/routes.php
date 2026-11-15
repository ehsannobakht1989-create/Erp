<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Modules\OrgStructure\Services\OrgStructureService;

return function (Router $router, Container $container): void {
    $router->get('/units', fn(Request $req): Response => Response::ok(
        $container->make(OrgStructureService::class)->unitsTree((int) ($req->attributes['company_id'] ?? 1))
    ), 'org-structure.unit.view');

    $router->post('/actions/manage-0', fn(): Response => Response::ok(['permission' => 'org-structure.unit.manage']), 'org-structure.unit.manage');
    $router->post('/actions/view-1', fn(): Response => Response::ok(['permission' => 'org-structure.cost-center.view']), 'org-structure.cost-center.view');
    $router->post('/actions/manage-2', fn(): Response => Response::ok(['permission' => 'org-structure.cost-center.manage']), 'org-structure.cost-center.manage');
    $router->post('/actions/manage-3', fn(): Response => Response::ok(['permission' => 'org-structure.calendar.manage']), 'org-structure.calendar.manage');
};
