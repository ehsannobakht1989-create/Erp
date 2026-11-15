<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Modules\Inventory\Services\InventoryService;

return function (Router $router, Container $container): void {
    $router->get('/balances', fn(Request $req): Response => Response::ok([
        'balance' => $container->make(InventoryService::class)->getBalance((int) ($req->attributes['company_id'] ?? 1), $req->query),
    ]), 'inventory.balance.view');

    $router->post('/actions/manage-0', fn(): Response => Response::ok(['permission' => 'inventory.warehouse.manage']), 'inventory.warehouse.manage');
    $router->post('/actions/view-1', fn(): Response => Response::ok(['permission' => 'inventory.receipt.view']), 'inventory.receipt.view');
    $router->post('/actions/create-2', fn(): Response => Response::ok(['permission' => 'inventory.receipt.create']), 'inventory.receipt.create');
    $router->post('/actions/post-3', fn(): Response => Response::ok(['permission' => 'inventory.receipt.post']), 'inventory.receipt.post');
    $router->post('/actions/post-4', fn(): Response => Response::ok(['permission' => 'inventory.issue.post']), 'inventory.issue.post');
};
