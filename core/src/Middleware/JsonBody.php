<?php
declare(strict_types=1);

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\Response;

final class JsonBody
{
    public function handle(Request $request, callable $next): Response
    {
        $contentType = $request->header('Content-Type', '');
        if (str_contains((string) $contentType, 'application/json') && $request->rawBody !== '') {
            $decoded = json_decode($request->rawBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return Response::error('INVALID_JSON', 'Malformed JSON request body', 400);
            }
            if (is_array($decoded)) {
                $request->body = $decoded;
            }
        }
        return $next($request);
    }
}
