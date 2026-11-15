<?php
declare(strict_types=1);

use Core\Http\Router;

return function (): void {
    $router = new Router();
    $router->group('/api/inventory', function (Router $r): void {
        $r->get('/items/{id}', fn() => 'ok', 'inventory.item.view', 'inventory.item.show');
    });

    $match = $router->match('GET', '/api/inventory/items/42');
    assertTrue($match !== null, 'Route should match');
    assertEquals('42', $match['params']['id']);
    assertEquals('inventory.item.view', $match['permission']);
    assertEquals('/api/inventory/items/42', $router->url('inventory.item.show', ['id' => 42]));
    assertTrue($router->match('POST', '/api/inventory/items/42') === null, 'Wrong method should not match');
};
