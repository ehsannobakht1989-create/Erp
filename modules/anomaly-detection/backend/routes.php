<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Response;
use Core\Http\Router;

return function (Router $router, Container $container): void {
    $router->get('/records', fn(): Response => Response::ok([]), 'anomaly-detection.record.view');

    $router->post('/actions/create-0', fn(): Response => Response::ok(['permission' => 'anomaly-detection.record.create']), 'anomaly-detection.record.create');
    $router->post('/actions/approve-1', fn(): Response => Response::ok(['permission' => 'anomaly-detection.record.approve']), 'anomaly-detection.record.approve');
};
