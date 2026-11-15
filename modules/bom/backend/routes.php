<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Modules\Bom\Services\BomService;

return function (Router $router, Container $container): void {
    $router->get('/boms', fn(Request $req): Response => Response::ok(
        $container->make(BomService::class)->explode((int) ($req->attributes['company_id'] ?? 1), $req->query)
    ), 'bom.bom.view');
};
