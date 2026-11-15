<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Modules\Pricing\Services\PricingService;

return function (Router $router, Container $container): void {
    $router->post('/resolve', fn(Request $req): Response => Response::ok(
        $container->make(PricingService::class)->resolve((int) ($req->attributes['company_id'] ?? 1), $req->body)
    ), 'pricing.list.view');

    $router->post('/actions/manage-0', fn(): Response => Response::ok(['permission' => 'pricing.list.manage']), 'pricing.list.manage');
    $router->post('/actions/approve-1', fn(): Response => Response::ok(['permission' => 'pricing.list.approve']), 'pricing.list.approve');
    $router->post('/actions/override-2', fn(): Response => Response::ok(['permission' => 'pricing.override']), 'pricing.override');
};
