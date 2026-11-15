<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Modules\AssetRegistry\Services\AssetRegistryService;

return function (Router $router, Container $container): void {
    $router->get('/assets', fn(Request $req): Response => Response::ok(
        $container->make(AssetRegistryService::class)->tree((int) ($req->attributes['company_id'] ?? 1))
    ), 'asset-registry.asset.view');
};
