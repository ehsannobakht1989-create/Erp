<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Response;
use Core\Http\Router;

return function (Router $router, Container $container): void {
    $router->get('/records', fn(): Response => Response::ok([]), 'quality-inspection.record.view');

    $router->post('/actions/create-0', fn(): Response => Response::ok(['permission' => 'quality-inspection.record.create']), 'quality-inspection.record.create');
    $router->post('/actions/approve-1', fn(): Response => Response::ok(['permission' => 'quality-inspection.record.approve']), 'quality-inspection.record.approve');
};
