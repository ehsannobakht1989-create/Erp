<?php
declare(strict_types=1);

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\Response;

final class Csrf
{
    public function handle(Request $request, callable $next): Response
    {
        if (in_array($request->method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $next($request);
        }

        // Only enforce CSRF for cookie-authenticated sessions (when session cookie is present without Bearer token)
        $authHeader = $request->header('Authorization', '');
        if (str_starts_with((string) $authHeader, 'Bearer ')) {
            return $next($request);
        }

        if (!empty($request->cookies)) {
            $cookieToken = $request->cookies['csrf_token'] ?? '';
            $headerToken = $request->header('X-CSRF-Token', '');
            if ($cookieToken === '' || !hash_equals($cookieToken, (string) $headerToken)) {
                return Response::error('CSRF_MISMATCH', 'Invalid CSRF token', 403);
            }
        }

        return $next($request);
    }
}
