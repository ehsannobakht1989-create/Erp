<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Response;
use Core\Http\Router;

return function (Router $router, Container $container): void {
    $router->get('/forms', fn(): Response => Response::ok([]), 'form-builder.form.view');

    $router->post('/actions/manage-0', fn(): Response => Response::ok(['permission' => 'form-builder.form.manage']), 'form-builder.form.manage');
    $router->post('/actions/create-1', fn(): Response => Response::ok(['permission' => 'form-builder.submission.create']), 'form-builder.submission.create');
    $router->post('/actions/use-2', fn(): Response => Response::ok(['permission' => 'form-builder.ai.use']), 'form-builder.ai.use');
};
