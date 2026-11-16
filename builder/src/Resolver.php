<?php

declare(strict_types=1);

namespace Builder;

use RuntimeException;

final class Resolver
{
    public function __construct(private readonly string $root) {}

    /**
     * @param  array<string, mixed>  $profile
     * @return array<string, mixed>
     */
    public function resolve(array $profile): array
    {
        $all = $this->manifests();

        $requested = (array) ($profile['modules'] ?? []);
        if (in_array('*', $requested, true)) {
            $requested = array_keys($all);
        }

        $excludeRegional = (array) ($profile['exclude_regional'] ?? []);
        $selected = [];
        $autoAdded = [];
        $warnings = [];

        $addWithDeps = function (string $id, ?string $neededBy = null) use (&$addWithDeps, &$selected, &$autoAdded, &$warnings, $all, $excludeRegional): void {
            if (! isset($all[$id])) {
                throw new RuntimeException("Unknown module in profile: '$id'");
            }
            $manifest = $all[$id];
            foreach ($manifest['regional'] ?? [] as $reg) {
                if (in_array($reg, $excludeRegional, true)) {
                    $warnings[] = "Excluded regional module '$id' ($reg)";

                    return;
                }
            }
            if (! isset($selected[$id])) {
                $selected[$id] = true;
                if ($neededBy !== null) {
                    $autoAdded[$id] = $neededBy;
                }
            }
            foreach ($manifest['requires'] ?? [] as $dep) {
                if (! isset($selected[$dep])) {
                    $addWithDeps($dep, $id);
                }
            }
        };

        foreach ($requested as $id) {
            $addWithDeps((string) $id, null);
        }

        // Preserve dependency order from manifests()
        $ordered = [];
        foreach ($all as $id => $manifest) {
            if (isset($selected[$id])) {
                $ordered[] = $id;
            }
        }

        $aiEnabled = (bool) ($profile['ai']['enabled'] ?? false);
        $pythonNeeded = false;
        foreach ($ordered as $id) {
            if (! empty($all[$id]['python']) && $aiEnabled) {
                $pythonNeeded = true;
                break;
            }
        }

        return [
            'factory' => $profile['factory'] ?? ['name' => 'Factory', 'slug' => 'factory'],
            'modules' => $ordered,
            'auto_added' => $autoAdded,
            'warnings' => $warnings,
            'python_needed' => $pythonNeeded,
            'ai' => $profile['ai'] ?? ['enabled' => false, 'provider' => 'off'],
            'deploy' => $profile['deploy'] ?? ['target' => 'cpanel'],
        ];
    }

    /**
     * Module manifests keyed by id in dependency order (hard requires first, then id order).
     *
     * @return array<string, array<string, mixed>>
     */
    public function manifests(): array
    {
        $manifests = [];
        foreach (glob($this->root.'/modules/*/module.json') ?: [] as $path) {
            $manifest = json_decode((string) file_get_contents($path), true);
            $id = basename(dirname($path));
            if (! is_array($manifest) || ($manifest['id'] ?? null) !== $id) {
                throw new RuntimeException("Invalid manifest: modules/$id/module.json");
            }
            $manifests[$id] = $manifest;
        }
        ksort($manifests, SORT_STRING);
        $ordered = [];
        $visiting = [];
        $visit = function (string $id) use (&$visit, &$ordered, &$visiting, $manifests): void {
            if (isset($ordered[$id])) {
                return;
            }
            if (isset($visiting[$id])) {
                throw new RuntimeException("Dependency cycle at module '$id'");
            }
            $visiting[$id] = true;
            foreach ($manifests[$id]['requires'] ?? [] as $dependency) {
                if (! isset($manifests[$dependency])) {
                    throw new RuntimeException("Module '$id' requires unknown module '$dependency'");
                }
                $visit((string) $dependency);
            }
            unset($visiting[$id]);
            $ordered[$id] = $manifests[$id];
        };
        foreach (array_keys($manifests) as $id) {
            $visit($id);
        }

        return $ordered;
    }
}
