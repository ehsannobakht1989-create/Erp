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
};
