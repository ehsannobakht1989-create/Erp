<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Response;
use Core\Http\Router;

return function (Router $router, Container $container): void {
    $router->get('/tasks', fn(): Response => Response::ok([]), 'workflow-engine.task.view');
};
