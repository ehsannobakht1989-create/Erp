<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Response;
use Core\Http\Router;

return function (Router $router, Container $container): void {
    $router->get('/tasks', fn(): Response => Response::ok([]), 'workflow-engine.task.view');

    $router->post('/actions/act-0', fn(): Response => Response::ok(['permission' => 'workflow-engine.task.act']), 'workflow-engine.task.act');
    $router->post('/actions/manage-1', fn(): Response => Response::ok(['permission' => 'workflow-engine.definition.manage']), 'workflow-engine.definition.manage');
    $router->post('/actions/use-2', fn(): Response => Response::ok(['permission' => 'workflow-engine.ai.use']), 'workflow-engine.ai.use');
};
