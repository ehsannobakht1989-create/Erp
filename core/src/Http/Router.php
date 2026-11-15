<?php
declare(strict_types=1);

namespace Core\Http;

final class Router
{
    /** @var array<int, array<string, mixed>> */
    private array $routes = [];

    /** @var array<string, array<string, mixed>> */
    private array $named = [];

    private string $prefix = '';

    /** @var array<int, string|callable> */
    private array $groupMiddleware = [];

    public function group(string $prefix, callable $callback, array $middleware = []): void
    {
        $prevPrefix = $this->prefix;
        $prevMw = $this->groupMiddleware;

        $this->prefix = rtrim($prevPrefix, '/') . '/' . trim($prefix, '/');
        if ($this->prefix !== '/') {
            $this->prefix = '/' . ltrim($this->prefix, '/');
        }
        $this->groupMiddleware = array_merge($prevMw, $middleware);

        $callback($this);

        $this->prefix = $prevPrefix;
        $this->groupMiddleware = $prevMw;
    }

    public function get(string $path, callable|array|string $handler, ?string $permission = null, ?string $name = null): self
    {
        return $this->add('GET', $path, $handler, $permission, $name);
    }

    public function post(string $path, callable|array|string $handler, ?string $permission = null, ?string $name = null): self
    {
        return $this->add('POST', $path, $handler, $permission, $name);
    }

    public function put(string $path, callable|array|string $handler, ?string $permission = null, ?string $name = null): self
    {
        return $this->add('PUT', $path, $handler, $permission, $name);
    }

    public function patch(string $path, callable|array|string $handler, ?string $permission = null, ?string $name = null): self
    {
        return $this->add('PATCH', $path, $handler, $permission, $name);
    }

    public function delete(string $path, callable|array|string $handler, ?string $permission = null, ?string $name = null): self
    {
        return $this->add('DELETE', $path, $handler, $permission, $name);
    }

    public function add(
        string $method,
        string $path,
        callable|array|string $handler,
        ?string $permission = null,
        ?string $name = null,
    ): self {
        $fullPath = $this->normalizePath($this->prefix . '/' . ltrim($path, '/'));
        $regex = $this->compileRegex($fullPath);
        $route = [
            'method' => strtoupper($method),
            'path' => $fullPath,
            'regex' => $regex,
            'handler' => $handler,
            'permission' => $permission,
            'middleware' => $this->groupMiddleware,
            'name' => $name,
        ];
        $this->routes[] = $route;
        if ($name !== null) {
            $this->named[$name] = $route;
        }
        return $this;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function match(string $method, string $path): ?array
    {
        $method = strtoupper($method);
        $path = $this->normalizePath($path);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['regex'], $path, $matches)) {
                $params = [];
                foreach ($matches as $k => $v) {
                    if (is_string($k)) {
                        $params[$k] = $v;
                    }
                }
                $route['params'] = $params;
                return $route;
            }
        }
        return null;
    }

    /** @param array<string, string|int> $params */
    public function url(string $name, array $params = []): string
    {
        if (!isset($this->named[$name])) {
            throw new \RuntimeException("Route [$name] not found");
        }
        $path = $this->named[$name]['path'];
        foreach ($params as $k => $v) {
            $path = str_replace('{' . $k . '}', (string) $v, $path);
        }
        return $path;
    }

    private function normalizePath(string $path): string
    {
        $p = '/' . trim($path, '/');
        return $p === '//' ? '/' : $p;
    }

    private function compileRegex(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
}
