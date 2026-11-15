<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Modules\AuthRbac\AuthService;

return function (Router $router, Container $container): void {
    $router->post('/login', function (Request $req) use ($container): Response {
        $auth = $container->make(AuthService::class);
        $res = $auth->login(
            (int) ($req->input('company_id', 1)),
            (string) $req->input('username', ''),
            (string) $req->input('password', ''),
            $req->ip()
        );
        return $res['ok'] ? Response::ok($res) : Response::error('AUTH_FAILED', $res['error'] ?? 'Failed', 401);
    });

    $router->get('/users', fn(): Response => Response::ok([]), 'auth-rbac.user.view');
    $router->get('/roles', fn(): Response => Response::ok([]), 'auth-rbac.role.view');
    $router->get('/_meta/field-hints', fn(): Response => Response::ok([]), 'auth-rbac.user.view');

    $router->post('/actions/create-0', fn(): Response => Response::ok(['permission' => 'auth-rbac.user.create']), 'auth-rbac.user.create');
    $router->post('/actions/update-1', fn(): Response => Response::ok(['permission' => 'auth-rbac.user.update']), 'auth-rbac.user.update');
    $router->post('/actions/manage-2', fn(): Response => Response::ok(['permission' => 'auth-rbac.role.manage']), 'auth-rbac.role.manage');
    $router->post('/actions/use-3', fn(): Response => Response::ok(['permission' => 'auth-rbac.ai.use']), 'auth-rbac.ai.use');
};
