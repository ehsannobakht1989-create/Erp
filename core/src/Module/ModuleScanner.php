<?php
declare(strict_types=1);

namespace Core\Module;

use RuntimeException;

final class ModuleScanner
{
    public function __construct(private readonly string $root)
    {
    }

    /**
     * @return array<string, array<string, mixed>> Topologically sorted manifests keyed by module id
     */
    public function scan(): array
    {
        $files = glob($this->root . '/modules/*/module.json') ?: [];
        $manifests = [];

        foreach ($files as $file) {
            $dirName = basename(dirname($file));
            $raw = file_get_contents($file);
            $data = $raw !== false ? json_decode($raw, true) : null;
            if (!is_array($data)) {
                throw new RuntimeException("Invalid JSON in modules/$dirName/module.json");
            }
            $this->validateManifest($dirName, $data);
            $manifests[$data['id']] = $data;
        }

        // Validate hard dependencies exist (optional deps are ignored if absent)
        foreach ($manifests as $id => $data) {
            foreach ($data['requires'] ?? [] as $dep) {
                if (!isset($manifests[$dep])) {
                    throw new RuntimeException("Module '$id' requires unknown module '$dep'");
                }
            }
        }

        return $this->topologicalSort($manifests);
    }

    /** @param array<string, mixed> $data */
    private function validateManifest(string $dirName, array $data): void
    {
        foreach (['id', 'name', 'version', 'category', 'level'] as $req) {
            if (!array_key_exists($req, $data)) {
                throw new RuntimeException("[$dirName] Missing required field '$req'");
            }
        }
        $id = (string) $data['id'];
        if (!preg_match('/^[a-z][a-z0-9-]*$/', $id) || $id !== $dirName) {
            throw new RuntimeException("[$dirName] Invalid or mismatched module id '$id'");
        }
    }

    /**
     * @param array<string, array<string, mixed>> $manifests
     * @return array<string, array<string, mixed>>
     */
    private function topologicalSort(array $manifests): array
    {
        $state = []; // 0=unvisited, 1=visiting, 2=visited
        $sorted = [];

        $visit = function (string $id, array $stack) use (&$visit, &$state, &$sorted, $manifests): void {
            $state[$id] = 1;
            foreach ($manifests[$id]['requires'] ?? [] as $dep) {
                $s = $state[$dep] ?? 0;
                if ($s === 1) {
                    throw new RuntimeException('Dependency cycle detected: ' . implode(' -> ', array_merge($stack, [$id, $dep])));
                }
                if ($s === 0) {
                    $visit($dep, array_merge($stack, [$id]));
                }
            }
            $state[$id] = 2;
            $sorted[$id] = $manifests[$id];
        };

        $ids = array_keys($manifests);
        sort($ids, SORT_STRING);
        foreach ($ids as $id) {
            if (($state[$id] ?? 0) === 0) {
                $visit($id, []);
            }
        }

        return $sorted;
    }
}
