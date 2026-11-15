<?php
declare(strict_types=1);

namespace Core\Middleware;

use Core\Contracts\AuthContract;
use Core\Http\Request;
use Core\Http\Response;

final class AuthGuard
{
    public function __construct(private readonly AuthContract $auth)
    {
    }

    public function handle(Request $request, callable $next): Response
    {
        $user = $this->auth->authenticate($request);
        if ($user === null) {
            return Response::error('UNAUTHORIZED', 'Authentication required', 401);
        }
        $request->attributes['user'] = $user;
        return $next($request);
    }
}
