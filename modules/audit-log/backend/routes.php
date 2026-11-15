<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Response;
use Core\Http\Router;

return function (Router $router, Container $container): void {
    $router->get('/entries', fn(): Response => Response::ok([]), 'audit-log.entry.view');
};
