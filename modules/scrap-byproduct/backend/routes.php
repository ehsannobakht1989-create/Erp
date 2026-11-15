<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Response;
use Core\Http\Router;

return function (Router $router, Container $container): void {
    $router->get('/records', fn(): Response => Response::ok([]), 'scrap-byproduct.record.view');

    $router->post('/actions/create-0', fn(): Response => Response::ok(['permission' => 'scrap-byproduct.record.create']), 'scrap-byproduct.record.create');
    $router->post('/actions/approve-1', fn(): Response => Response::ok(['permission' => 'scrap-byproduct.record.approve']), 'scrap-byproduct.record.approve');
};
