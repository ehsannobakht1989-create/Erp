<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Router;
use Modules\ItemMaster\Controllers\ItemController;

return function (Router $router, Container $container): void {
    $router->get('/items', fn(Request $req) => $container->make(ItemController::class)->index($req), 'item-master.item.view');
    $router->post('/items', fn(Request $req) => $container->make(ItemController::class)->store($req), 'item-master.item.create');

    $router->post('/actions/update-0', fn(): Response => Response::ok(['permission' => 'item-master.item.update']), 'item-master.item.update');
    $router->post('/actions/delete-1', fn(): Response => Response::ok(['permission' => 'item-master.item.delete']), 'item-master.item.delete');
    $router->post('/actions/approve-2', fn(): Response => Response::ok(['permission' => 'item-master.item.approve']), 'item-master.item.approve');
    $router->post('/actions/import-3', fn(): Response => Response::ok(['permission' => 'item-master.item.import']), 'item-master.item.import');
    $router->post('/actions/export-4', fn(): Response => Response::ok(['permission' => 'item-master.item.export']), 'item-master.item.export');
    $router->post('/actions/merge-5', fn(): Response => Response::ok(['permission' => 'item-master.item.merge']), 'item-master.item.merge');
    $router->post('/actions/manage-6', fn(): Response => Response::ok(['permission' => 'item-master.category.manage']), 'item-master.category.manage');
    $router->post('/actions/manage-7', fn(): Response => Response::ok(['permission' => 'item-master.coding.manage']), 'item-master.coding.manage');
    $router->post('/actions/manage-8', fn(): Response => Response::ok(['permission' => 'item-master.alternate.manage']), 'item-master.alternate.manage');
    $router->post('/actions/use-9', fn(): Response => Response::ok(['permission' => 'item-master.ai.use']), 'item-master.ai.use');
};
