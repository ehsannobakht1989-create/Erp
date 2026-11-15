<?php
declare(strict_types=1);

namespace Core;

final class Config
{
    /** @var array<string, mixed> */
    private array $items = [];

    /** @param array<string, mixed> $items */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $current = $this->items;
        foreach ($segments as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return $default;
            }
            $current = $current[$segment];
        }
        return $current;
    }

    public function set(string $key, mixed $value): void
    {
        $segments = explode('.', $key);
        $ref = &$this->items;
        foreach ($segments as $i => $segment) {
            if ($i === count($segments) - 1) {
                $ref[$segment] = $value;
            } else {
                if (!isset($ref[$segment]) || !is_array($ref[$segment])) {
                    $ref[$segment] = [];
                }
                $ref = &$ref[$segment];
            }
        }
    }

    public function string(string $key, string $default = ''): string
    {
        $v = $this->get($key, $default);
        return is_scalar($v) ? (string) $v : $default;
    }

    public function int(string $key, int $default = 0): int
    {
        $v = $this->get($key, $default);
        return is_numeric($v) ? (int) $v : $default;
    }

    public function bool(string $key, bool $default = false): bool
    {
        return (bool) $this->get($key, $default);
    }
}
