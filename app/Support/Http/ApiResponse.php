<?php

declare(strict_types=1);

namespace App\Support\Http;

use Illuminate\Http\JsonResponse;

/** Standard envelope: {ok, data, error, meta}. */
final class ApiResponse
{
    /** @param array<string, mixed> $meta */
    public static function ok(mixed $data = null, array $meta = [], int $status = 200): JsonResponse
    {
        return new JsonResponse(['ok' => true, 'data' => $data, 'error' => null, 'meta' => (object) $meta], $status);
    }

    /** @param array<string, mixed> $details
     * @param  array<string, mixed>  $meta
     */
    public static function error(string $code, string $message, int $status, array $details = [], array $meta = []): JsonResponse
    {
        return new JsonResponse([
            'ok' => false,
            'data' => null,
            'error' => ['code' => $code, 'message' => $message, 'details' => (object) $details],
            'meta' => (object) $meta,
        ], $status);
    }
}
