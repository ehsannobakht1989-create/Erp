<?php

declare(strict_types=1);

namespace App\Support\Ai;

use Illuminate\Http\Client\Factory;
use RuntimeException;

/** HMAC-SHA256 over "timestamp\nrequest_id\nbody"; the service must reject >300s skew and replays. */
final class PythonServiceProvider implements AiProvider
{
    public function __construct(private readonly Factory $http, private readonly string $url, private readonly string $secret, private readonly int $timeout = 30) {}

    public static function signature(string $secret, string $timestamp, string $requestId, string $body): string
    {
        return 'sha256='.hash_hmac('sha256', $timestamp."\n".$requestId."\n".$body, $secret);
    }

    public function complete(array $request): array
    {
        if (strlen($this->secret) < 32 || ! str_starts_with($this->url, 'http')) {
            throw new RuntimeException('Python AI service is not configured.');
        }
        $body = json_encode(['input' => $request['input'], 'max_tokens' => $request['max_tokens']], JSON_THROW_ON_ERROR);
        $timestamp = (string) time();
        $response = $this->http->timeout($this->timeout)->withHeaders([
            'X-ERP-Timestamp' => $timestamp,
            'X-ERP-Request-Id' => $request['request_id'],
            'X-ERP-Signature' => self::signature($this->secret, $timestamp, $request['request_id'], $body),
        ])->withBody($body, 'application/json')->post(rtrim($this->url, '/').'/v1/capabilities/'.$request['module'].'/'.$request['capability'])->throw();
        $data = $response->json();
        if (! is_array($data) || ! array_key_exists('output', $data)) {
            throw new RuntimeException('Invalid Python AI service response.');
        }

        return ['output' => $data['output'], 'tool_calls' => [], 'proposals' => is_array($data['proposals'] ?? null) ? array_values($data['proposals']) : [], 'usage' => ['input_tokens' => (int) ($data['usage']['input_tokens'] ?? 0), 'output_tokens' => (int) ($data['usage']['output_tokens'] ?? 0)]];
    }
}
