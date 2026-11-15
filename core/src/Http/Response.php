<?php
declare(strict_types=1);

namespace Core\Http;

final class Response
{
    /** @param array<string, string> $headers */
    public function __construct(
        public readonly int $status = 200,
        public readonly string $body = '',
        public array $headers = ['Content-Type' => 'application/json; charset=utf-8'],
    ) {
    }

    /**
     * @param array<string, mixed> $meta
     */
    public static function ok(mixed $data = null, array $meta = [], int $status = 200): self
    {
        $payload = [
            'ok' => true,
            'data' => $data,
            'error' => null,
            'meta' => (object) $meta,
        ];
        return new self($status, (string) json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * @param array<string, mixed> $details
     */
    public static function error(string $code, string $message, int $status = 400, array $details = []): self
    {
        $payload = [
            'ok' => false,
            'data' => null,
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => (object) $details,
            ],
            'meta' => (object) [],
        ];
        return new self($status, (string) json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function withHeader(string $name, string $value): self
    {
        $clone = clone $this;
        $clone->headers[$name] = $value;
        return $clone;
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $k => $v) {
            header("$k: $v");
        }
        echo $this->body;
    }
}
