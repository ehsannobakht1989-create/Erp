<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Modules\Bom\Services\BomService;

return function (Router $router, Container $container): void {
    $router->get('/boms', fn(Request $req): Response => Response::ok(
        $container->make(BomService::class)->explode((int) ($req->attributes['company_id'] ?? 1), $req->query)
    ), 'bom.bom.view');

    $router->post('/actions/create-0', fn(): Response => Response::ok(['permission' => 'bom.bom.create']), 'bom.bom.create');
    $router->post('/actions/update-1', fn(): Response => Response::ok(['permission' => 'bom.bom.update']), 'bom.bom.update');
    $router->post('/actions/approve-2', fn(): Response => Response::ok(['permission' => 'bom.bom.approve']), 'bom.bom.approve');
    $router->post('/actions/delete-3', fn(): Response => Response::ok(['permission' => 'bom.bom.delete']), 'bom.bom.delete');
};
