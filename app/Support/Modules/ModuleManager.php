<?php

declare(strict_types=1);

namespace App\Support\Modules;

use Composer\Autoload\ClassLoader;
use Illuminate\Contracts\Foundation\Application;
use RuntimeException;

/** Registers providers for explicitly enabled modules, in dependency order. */
final class ModuleManager
{
    private bool $registered = false;

    /** @var list<string> */
    private array $enabled = [];

    public function __construct(
        private readonly Application $app,
        private readonly ModuleCatalog $catalog,
        private readonly ClassLoader $loader,
        private readonly string $modulesPath,
    ) {}

    /**
     * Modules whose providers were registered at boot; runtime services use this set so a
     * later database override cannot expose a module whose code is not loaded in this process.
     *
     * @return list<string>
     */
    public function enabled(): array
    {
        if (! $this->registered) {
            throw new RuntimeException('Module providers have not been registered yet.');
        }

        return $this->enabled;
    }

    /** @param list<string> $enabled */
    public function registerEnabled(array $enabled): void
    {
        if ($this->registered) {
            throw new RuntimeException('Module providers have already been registered.');
        }
        $ordered = $this->catalog->ordered($enabled);
        $providers = [];
        $namespaces = [];
        // Validate every file before registering any module providers.
        foreach ($ordered as $id) {
            $studly = implode('', array_map('ucfirst', explode('-', $id)));
            $namespace = 'Modules\\'.$studly.'\\';
            if (isset($namespaces[$namespace])) {
                throw new RuntimeException("Ambiguous module namespace for $id");
            }
            $namespaces[$namespace] = true;
            $path = rtrim($this->modulesPath, '/').'/'.$id;
            if (! is_file($path.'/Providers/ModuleServiceProvider.php')) {
                throw new RuntimeException("Enabled module $id has no Laravel provider.");
            }
            $providers[] = [$namespace, $path];
        }
        foreach ($providers as [$namespace, $path]) {
            $this->loader->addPsr4($namespace, [$path.'/src/', $path.'/']);
            $this->loader->addPsr4($namespace.'Database\\Seeders\\', $path.'/database/seeders/');
            $class = $namespace.'Providers\\ModuleServiceProvider';
            if (! is_subclass_of($class, ModuleServiceProvider::class)) {
                throw new RuntimeException("Module provider $class must extend ".ModuleServiceProvider::class);
            }
        }
        foreach ($providers as [$namespace]) {
            $this->app->register($namespace.'Providers\\ModuleServiceProvider');
        }
        $this->registered = true;
        $this->enabled = $ordered;
    }
}
