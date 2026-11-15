<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Modules\AccountingGl\Services\AccountingGlService;

return function (Router $router, Container $container): void {
    $router->get('/journals', fn(Request $req): Response => Response::ok(
        $container->make(AccountingGlService::class)->trialBalance((int) ($req->attributes['company_id'] ?? 1))
    ), 'accounting-gl.journal.view');
};
