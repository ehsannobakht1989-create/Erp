<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Response;
use Core\Http\Router;

return function (Router $router, Container $container): void {
    $router->get('/forms', fn(): Response => Response::ok([]), 'form-builder.form.view');
};
