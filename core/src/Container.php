<?php
declare(strict_types=1);

namespace Core;

use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

final class Container
{
    /** @var array<string, callable|object|string> */
    private array $bindings = [];

    /** @var array<string, bool> */
    private array $singletons = [];

    /** @var array<string, object> */
    private array $instances = [];

    public function bind(string $abstract, callable|string $concrete, bool $singleton = false): void
    {
        $this->bindings[$abstract] = $concrete;
        $this->singletons[$abstract] = $singleton;
    }

    public function singleton(string $abstract, callable|string|object $concrete): void
    {
        if (is_object($concrete) && !($concrete instanceof \Closure)) {
            $this->instances[$abstract] = $concrete;
            $this->singletons[$abstract] = true;
            return;
        }
        $this->bind($abstract, $concrete, true);
    }

    public function has(string $abstract): bool
    {
        return isset($this->instances[$abstract]) || isset($this->bindings[$abstract]) || class_exists($abstract);
    }

    public function make(string $abstract): object
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->bindings[$abstract] ?? $abstract;

        if ($concrete instanceof \Closure) {
            $object = $concrete($this);
        } elseif (is_string($concrete)) {
            $object = $this->autowire($concrete);
        } else {
            throw new RuntimeException("Cannot resolve [$abstract]");
        }

        if ($this->singletons[$abstract] ?? false) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    private function autowire(string $class): object
    {
        if (!class_exists($class)) {
            throw new RuntimeException("Class [$class] does not exist");
        }
        $ref = new ReflectionClass($class);
        if (!$ref->isInstantiable()) {
            throw new RuntimeException("Class [$class] is not instantiable");
        }
        $ctor = $ref->getConstructor();
        if ($ctor === null || $ctor->getNumberOfParameters() === 0) {
            return new $class();
        }

        $args = [];
        foreach ($ctor->getParameters() as $param) {
            $type = $param->getType();
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $args[] = $this->make($type->getName());
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                throw new RuntimeException("Cannot resolve parameter \${$param->getName()} of [$class]");
            }
        }
        return $ref->newInstanceArgs($args);
    }
}
