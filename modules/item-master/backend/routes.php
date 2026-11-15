<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Router;
use Modules\ItemMaster\Controllers\ItemController;

return function (Router $router, Container $container): void {
    $router->get('/items', fn(Request $req) => $container->make(ItemController::class)->index($req), 'item-master.item.view');
    $router->post('/items', fn(Request $req) => $container->make(ItemController::class)->store($req), 'item-master.item.create');
};
