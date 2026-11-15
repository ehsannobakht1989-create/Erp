<?php
declare(strict_types=1);

namespace Core\Http;

final class MiddlewarePipeline
{
    /** @var array<int, callable|object> */
    private array $middlewares = [];

    public function pipe(callable|object $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function handle(Request $request, callable $destination): Response
    {
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            function (callable $next, callable|object $middleware): callable {
                return function (Request $req) use ($middleware, $next): Response {
                    if (is_object($middleware) && method_exists($middleware, 'handle')) {
                        return $middleware->handle($req, $next);
                    }
                    return $middleware($req, $next);
                };
            },
            $destination
        );

        return $pipeline($request);
    }
}
