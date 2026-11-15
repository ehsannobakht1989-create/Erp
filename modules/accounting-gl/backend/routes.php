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

    $router->post('/actions/manage-0', fn(): Response => Response::ok(['permission' => 'accounting-gl.account.manage']), 'accounting-gl.account.manage');
    $router->post('/actions/create-1', fn(): Response => Response::ok(['permission' => 'accounting-gl.journal.create']), 'accounting-gl.journal.create');
    $router->post('/actions/approve-2', fn(): Response => Response::ok(['permission' => 'accounting-gl.journal.approve']), 'accounting-gl.journal.approve');
    $router->post('/actions/view-3', fn(): Response => Response::ok(['permission' => 'accounting-gl.report.view']), 'accounting-gl.report.view');
};
