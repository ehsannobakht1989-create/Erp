<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Router;
use Modules\PartyMaster\Controllers\PartyController;

return function (Router $router, Container $container): void {
    $router->get('/parties', fn(Request $req) => $container->make(PartyController::class)->index($req), 'party-master.party.view');
    $router->post('/parties', fn(Request $req) => $container->make(PartyController::class)->store($req), 'party-master.party.create');

    $router->post('/actions/update-0', fn(): Response => Response::ok(['permission' => 'party-master.party.update']), 'party-master.party.update');
    $router->post('/actions/approve-1', fn(): Response => Response::ok(['permission' => 'party-master.party.approve']), 'party-master.party.approve');
    $router->post('/actions/delete-2', fn(): Response => Response::ok(['permission' => 'party-master.party.delete']), 'party-master.party.delete');
    $router->post('/actions/merge-3', fn(): Response => Response::ok(['permission' => 'party-master.party.merge']), 'party-master.party.merge');
    $router->post('/actions/import-4', fn(): Response => Response::ok(['permission' => 'party-master.party.import']), 'party-master.party.import');
    $router->post('/actions/export-5', fn(): Response => Response::ok(['permission' => 'party-master.party.export']), 'party-master.party.export');
    $router->post('/actions/manage-6', fn(): Response => Response::ok(['permission' => 'party-master.group.manage']), 'party-master.group.manage');
    $router->post('/actions/manage-7', fn(): Response => Response::ok(['permission' => 'party-master.payment-term.manage']), 'party-master.payment-term.manage');
    $router->post('/actions/use-8', fn(): Response => Response::ok(['permission' => 'party-master.ai.use']), 'party-master.ai.use');
};
