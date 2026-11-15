<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Router;
use Modules\PartyMaster\Controllers\PartyController;

return function (Router $router, Container $container): void {
    $router->get('/parties', fn(Request $req) => $container->make(PartyController::class)->index($req), 'party-master.party.view');
    $router->post('/parties', fn(Request $req) => $container->make(PartyController::class)->store($req), 'party-master.party.create');
};
