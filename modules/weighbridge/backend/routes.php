<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Response;
use Core\Http\Router;

return function (Router $router, Container $container): void {
    $router->get('/records', fn(): Response => Response::ok([]), 'weighbridge.record.view');

    $router->post('/actions/create-0', fn(): Response => Response::ok(['permission' => 'weighbridge.record.create']), 'weighbridge.record.create');
    $router->post('/actions/approve-1', fn(): Response => Response::ok(['permission' => 'weighbridge.record.approve']), 'weighbridge.record.approve');
};
