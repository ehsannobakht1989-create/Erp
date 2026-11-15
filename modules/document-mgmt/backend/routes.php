<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Response;
use Core\Http\Router;

return function (Router $router, Container $container): void {
    $router->get('/documents', fn(): Response => Response::ok([]), 'document-mgmt.document.view');

    $router->post('/actions/upload-0', fn(): Response => Response::ok(['permission' => 'document-mgmt.document.upload']), 'document-mgmt.document.upload');
    $router->post('/actions/use-1', fn(): Response => Response::ok(['permission' => 'document-mgmt.ai.use']), 'document-mgmt.ai.use');
};
