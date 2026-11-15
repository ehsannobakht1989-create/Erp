<?php
declare(strict_types=1);

namespace Core\Http;

final class Request
{
    /** @var array<string, mixed> */
    public array $attributes = [];

    /** @var array<string, string> */
    public array $params = [];

    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $body
     * @param array<string, string> $headers
     * @param array<string, string> $cookies
     * @param array<string, mixed> $server
     */
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public array $query = [],
        public array $body = [],
        public array $headers = [],
        public array $cookies = [],
        public array $server = [],
        public readonly string $rawBody = '',
    ) {
    }

    public static function fromGlobals(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $headers = [];
        foreach ($_SERVER as $k => $v) {
            if (str_starts_with($k, 'HTTP_')) {
                $name = str_replace('_', '-', strtolower(substr($k, 5)));
                $headers[$name] = (string) $v;
            }
        }
        $rawBody = (string) (file_get_contents('php://input') ?: '');
        return new self($method, $path, $_GET, $_POST, $headers, $_COOKIE, $_SERVER, $rawBody);
    }

    public function header(string $name, ?string $default = null): ?string
    {
        return $this->headers[strtolower($name)] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function ip(): string
    {
        return (string) ($this->server['REMOTE_ADDR'] ?? '127.0.0.1');
    }
}
