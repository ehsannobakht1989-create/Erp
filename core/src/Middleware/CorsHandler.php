<?php
declare(strict_types=1);

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\Response;

final class CorsHandler
{
    /** @param array<int, string> $allowedOrigins */
    public function __construct(private readonly array $allowedOrigins = ['*'])
    {
    }

    public function handle(Request $request, callable $next): Response
    {
        $origin = $request->header('Origin', '*');
        $allowOrigin = in_array('*', $this->allowedOrigins, true)
            ? '*'
            : (in_array($origin, $this->allowedOrigins, true) ? (string) $origin : '');

        if ($request->method === 'OPTIONS') {
            $res = new Response(204, '');
        } else {
            $res = $next($request);
        }

        if ($allowOrigin !== '') {
            $res = $res
                ->withHeader('Access-Control-Allow-Origin', $allowOrigin)
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Company-Id, X-CSRF-Token');
        }
        return $res;
    }
}
