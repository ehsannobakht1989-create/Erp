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
};
