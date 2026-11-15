<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Response;
use Core\Http\Router;

return function (Router $router, Container $container): void {
    $router->get('/keys', fn(): Response => Response::ok([]), 'api-webhooks.key.view');
    $router->get('/webhooks', fn(): Response => Response::ok([]), 'api-webhooks.key.view');
};
