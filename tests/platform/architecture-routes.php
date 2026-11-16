<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use Illuminate\Contracts\Console\Kernel;

$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$public = ['api/core/auth/login', 'api/core/auth/me', 'api/core/auth/logout', 'sanctum/csrf-cookie', 'up'];
$checked = 0;
foreach ($app['router']->getRoutes()->getRoutes() as $route) {
    $middleware = $route->gatherMiddleware();
    $guarded = count(array_filter($middleware, fn ($m) => is_string($m) && str_starts_with($m, 'can:'))) > 0;
    if (in_array($route->uri(), $public, true)) {
        continue;
    }
    if (! $guarded) {
        throw new RuntimeException('Route without can: permission middleware: '.$route->uri());
    }
    if (str_starts_with($route->uri(), 'api/') && ! str_starts_with($route->uri(), 'api/core/') && array_diff(['auth:sanctum', 'company.context'], $middleware) !== []) {
        throw new RuntimeException('Module API route lacks authentication or company context: '.$route->uri());
    }
    $checked++;
}
$uris = array_map(fn ($route) => $route->uri(), $app['router']->getRoutes()->getRoutes());
if (array_diff(['api/core/auth/login', 'api/core/auth/me', 'up'], $uris) !== []) {
    throw new RuntimeException('Expected core routes are missing; route architecture test would be vacuous');
}
echo "Route permission architecture tests ($checked guarded routes, ".count($public)." public allowlist entries) passed.\n";
