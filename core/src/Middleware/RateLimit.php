<?php
declare(strict_types=1);

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\Response;

final class RateLimit
{
    public function __construct(
        private readonly string $storageDir,
        private readonly int $maxRequests = 120,
        private readonly int $windowSeconds = 60,
    ) {
    }

    public function handle(Request $request, callable $next): Response
    {
        if (!is_dir($this->storageDir)) {
            @mkdir($this->storageDir, 0775, true);
        }
        $key = sha1($request->ip() . '|' . $request->path);
        $file = rtrim($this->storageDir, '/\\') . '/' . $key . '.json';
        $now = time();
        $data = ['count' => 0, 'reset' => $now + $this->windowSeconds];

        if (is_file($file)) {
            $raw = file_get_contents($file);
            $decoded = $raw !== false ? json_decode($raw, true) : null;
            if (is_array($decoded) && ($decoded['reset'] ?? 0) > $now) {
                $data = $decoded;
            }
        }

        $data['count'] = (int) $data['count'] + 1;
        @file_put_contents($file, (string) json_encode($data), LOCK_EX);

        if ($data['count'] > $this->maxRequests) {
            return Response::error('RATE_LIMITED', 'Too many requests', 429);
        }

        return $next($request);
    }
}
