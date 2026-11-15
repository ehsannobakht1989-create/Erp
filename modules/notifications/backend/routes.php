<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Response;
use Core\Http\Router;

return function (Router $router, Container $container): void {
    $router->get('/messages', fn(): Response => Response::ok([]), 'notifications.message.view');

    $router->post('/actions/manage-0', fn(): Response => Response::ok(['permission' => 'notifications.template.manage']), 'notifications.template.manage');
    $router->post('/actions/use-1', fn(): Response => Response::ok(['permission' => 'notifications.ai.use']), 'notifications.ai.use');
};
