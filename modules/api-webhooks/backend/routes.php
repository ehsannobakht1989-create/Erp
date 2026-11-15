<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Response;
use Core\Http\Router;

return function (Router $router, Container $container): void {
    $router->get('/keys', fn(): Response => Response::ok([]), 'api-webhooks.key.view');
    $router->get('/webhooks', fn(): Response => Response::ok([]), 'api-webhooks.key.view');

    $router->post('/actions/manage-0', fn(): Response => Response::ok(['permission' => 'api-webhooks.key.manage']), 'api-webhooks.key.manage');
    $router->post('/actions/manage-1', fn(): Response => Response::ok(['permission' => 'api-webhooks.webhook.manage']), 'api-webhooks.webhook.manage');
    $router->post('/actions/use-2', fn(): Response => Response::ok(['permission' => 'api-webhooks.ai.use']), 'api-webhooks.ai.use');
};
