<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Response;
use Core\Http\Router;

return function (Router $router, Container $container): void {
    $router->get('/templates', fn(): Response => Response::ok([]), 'print-barcode-qr.template.view');

    $router->post('/actions/manage-0', fn(): Response => Response::ok(['permission' => 'print-barcode-qr.template.manage']), 'print-barcode-qr.template.manage');
    $router->post('/actions/use-1', fn(): Response => Response::ok(['permission' => 'print-barcode-qr.ai.use']), 'print-barcode-qr.ai.use');
};
