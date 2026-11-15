<?php
declare(strict_types=1);

namespace Core;

final class Autoloader
{
    private string $root;

    public function __construct(string $root)
    {
        $this->root = rtrim($root, '/\\');
    }

    public function register(): void
    {
        spl_autoload_register([$this, 'load']);
    }

    public function load(string $class): void
    {
        if (str_starts_with($class, 'Core\\')) {
            $rel = substr($class, 5);
            $file = $this->root . '/core/src/' . str_replace('\\', '/', $rel) . '.php';
            if (is_file($file)) {
                require_once $file;
                return;
            }
            if (str_starts_with($rel, 'Contracts\\')) {
                $contractFile = $this->root . '/core/contracts/' . str_replace('\\', '/', substr($rel, 10)) . '.php';
                if (is_file($contractFile)) {
                    require_once $contractFile;
                    return;
                }
            }
        }

        if (str_starts_with($class, 'Modules\\')) {
            $parts = explode('\\', substr($class, 8), 2);
            if (count($parts) === 2) {
                $moduleId = $this->toKebab($parts[0]);
                $rel = str_replace('\\', '/', $parts[1]);
                $file = $this->root . '/modules/' . $moduleId . '/backend/src/' . $rel . '.php';
                if (is_file($file)) {
                    require_once $file;
                }
            }
        }
    }

    private function toKebab(string $studly): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '-$0', $studly));
    }
}
