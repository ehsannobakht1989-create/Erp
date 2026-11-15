<?php
declare(strict_types=1);

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Throwable;

final class ErrorHandler
{
    public function __construct(private readonly bool $debug = false)
    {
    }

    public function handle(Request $request, callable $next): Response
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            $details = $this->debug ? [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ] : [];
            $message = $this->debug ? $e->getMessage() : 'Internal server error';
            return Response::error('INTERNAL_ERROR', $message, 500, $details);
        }
    }
}
