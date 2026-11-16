<?php

declare(strict_types=1);

namespace App\Support\Modules;

use InvalidArgumentException;
use JsonException;
use RuntimeException;

/** Read-only module metadata and hard-dependency ordering; no providers are booted here. */
final class ModuleCatalog
{
    /** @param array<string, array<string, mixed>> $manifests */
    private function __construct(private readonly array $manifests) {}

    public static function scan(string $modulesPath): self
    {
        $files = glob(rtrim($modulesPath, '/').'/*/module.json');
        if ($files === false || $files === []) {
            throw new RuntimeException('No module manifests found.');
        }
        sort($files, SORT_STRING);
        $manifests = [];
        foreach ($files as $file) {
            try {
                $raw = file_get_contents($file);
                if ($raw === false) {
                    throw new RuntimeException("Cannot read $file");
                }
                $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new RuntimeException("Invalid JSON in $file", 0, $e);
            }
            $id = basename(dirname($file));
            if (! preg_match('/^[a-z][a-z0-9-]*$/D', $id) || ! is_array($data) || ($data['id'] ?? null) !== $id) {
                throw new RuntimeException("Invalid module id in $file");
            }
            foreach (['requires', 'optional'] as $key) {
                $dependencies = $data[$key] ?? null;
                if (! is_array($dependencies) || ! array_is_list($dependencies)) {
                    throw new RuntimeException("$id: $key must be a list");
                }
                $seen = [];
                foreach ($dependencies as $dependency) {
                    if (! is_string($dependency) || ! preg_match('/^[a-z][a-z0-9-]*$/D', $dependency) || isset($seen[$dependency])) {
                        throw new RuntimeException("$id: invalid or duplicate $key dependency");
                    }
                    $seen[$dependency] = true;
                }
            }
            $manifests[$id] = $data;
        }
        foreach ($manifests as $id => $manifest) {
            foreach ($manifest['requires'] as $dependency) {
                if (! isset($manifests[$dependency])) {
                    throw new RuntimeException("$id requires unknown module $dependency");
                }
            }
        }
        $catalog = new self($manifests);
        $catalog->ordered(array_keys($manifests)); // Reject cycles even in disabled modules.

        return $catalog;
    }

    /** @return array<string, array<string, mixed>> */
    public function manifests(): array
    {
        return $this->manifests;
    }

    /**
     * Return only enabled modules, dependencies first. Missing hard dependencies fail closed.
     * Optional dependencies do not imply activation or an ordering constraint.
     *
     * @param  list<string>  $enabled
     * @return list<string>
     */
    public function ordered(array $enabled): array
    {
        if (! array_is_list($enabled)) {
            throw new InvalidArgumentException('Enabled modules must be a list.');
        }
        $selected = [];
        foreach ($enabled as $id) {
            if (! is_string($id) || ! isset($this->manifests[$id])) {
                throw new InvalidArgumentException('Unknown enabled module: '.(is_scalar($id) ? (string) $id : get_debug_type($id)));
            }
            if (isset($selected[$id])) {
                throw new InvalidArgumentException("Duplicate enabled module: $id");
            }
            $selected[$id] = true;
        }
        $state = [];
        $ordered = [];
        $visit = function (string $id, array $path) use (&$visit, &$state, &$ordered, $selected): void {
            if (($state[$id] ?? 0) === 1) {
                throw new RuntimeException('Module dependency cycle: '.implode(' -> ', [...$path, $id]));
            }
            if (($state[$id] ?? 0) === 2) {
                return;
            }
            $state[$id] = 1;
            foreach ($this->manifests[$id]['requires'] as $dependency) {
                if (! isset($selected[$dependency])) {
                    throw new RuntimeException("$id requires disabled module $dependency");
                }
                $visit($dependency, [...$path, $id]);
            }
            $state[$id] = 2;
            $ordered[] = $id;
        };
        foreach ($enabled as $id) {
            $visit($id, []);
        }

        return $ordered;
    }
}
