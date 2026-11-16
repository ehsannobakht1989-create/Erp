<?php

declare(strict_types=1);

namespace App\Support\Modules;

use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;
use RuntimeException;

/** Override boot only when necessary, and call parent::boot to load owned resources. */
abstract class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $file = (new ReflectionClass($this))->getFileName();
        if ($file === false) {
            throw new RuntimeException('Module provider must have a source file.');
        }
        $path = dirname($file, 2);
        $id = basename($path);
        if (! preg_match('/^[a-z][a-z0-9-]*$/D', $id) || ! is_file($path.'/module.json')) {
            throw new RuntimeException('Module provider must live in its module Providers directory.');
        }
        if (is_dir($path.'/database/migrations')) {
            $this->loadMigrationsFrom($path.'/database/migrations');
        }
        if (is_dir($path.'/lang')) {
            $this->loadTranslationsFrom($path.'/lang', $id);
            $this->loadJsonTranslationsFrom($path.'/lang');
        }
        $routesCached = $this->app instanceof CachesRoutes && $this->app->routesAreCached();
        if (! $routesCached && is_file($path.'/routes/api.php')) {
            $router = $this->app->make(Router::class);
            $existing = $router->getRoutes()->getRoutes();
            $router->middleware(['api', 'auth:sanctum', 'company.context'])->prefix('api/'.$id)->group($path.'/routes/api.php');
            foreach (array_diff_key($router->getRoutes()->getRoutes(), $existing) as $route) {
                $permissionGuarded = false;
                foreach ($route->middleware() as $middleware) {
                    if (is_string($middleware) && str_starts_with($middleware, 'can:')) {
                        $permissionGuarded = true;
                    }
                }
                if (! $permissionGuarded) {
                    throw new RuntimeException('Module '.$id.' route '.$route->uri().' requires explicit can: permission middleware.');
                }
            }
        }
    }
}
