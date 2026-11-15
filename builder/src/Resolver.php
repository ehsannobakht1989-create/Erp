<?php
declare(strict_types=1);

namespace Builder;

use Core\Module\ModuleScanner;
use RuntimeException;

final class Resolver
{
    public function __construct(private readonly string $root)
    {
    }

    /**
     * @param array<string, mixed> $profile
     * @return array<string, mixed>
     */
    public function resolve(array $profile): array
    {
        $scanner = new ModuleScanner($this->root);
        $all = $scanner->scan();

        $requested = (array) ($profile['modules'] ?? []);
        if (in_array('*', $requested, true)) {
            $requested = array_keys($all);
        }

        $excludeRegional = (array) ($profile['exclude_regional'] ?? []);
        $selected = [];
        $autoAdded = [];
        $warnings = [];

        $addWithDeps = function (string $id, ?string $neededBy = null) use (&$addWithDeps, &$selected, &$autoAdded, &$warnings, $all, $excludeRegional): void {
            if (!isset($all[$id])) {
                throw new RuntimeException("Unknown module in profile: '$id'");
            }
            $manifest = $all[$id];
            foreach ($manifest['regional'] ?? [] as $reg) {
                if (in_array($reg, $excludeRegional, true)) {
                    $warnings[] = "Excluded regional module '$id' ($reg)";
                    return;
                }
            }
            if (!isset($selected[$id])) {
                $selected[$id] = true;
                if ($neededBy !== null) {
                    $autoAdded[$id] = $neededBy;
                }
            }
            foreach ($manifest['requires'] ?? [] as $dep) {
                if (!isset($selected[$dep])) {
                    $addWithDeps($dep, $id);
                }
            }
        };

        foreach ($requested as $id) {
            $addWithDeps((string) $id, null);
        }

        // Preserve topological order from ModuleScanner
        $ordered = [];
        foreach ($all as $id => $manifest) {
            if (isset($selected[$id])) {
                $ordered[] = $id;
            }
        }

        $aiEnabled = (bool) ($profile['ai']['enabled'] ?? false);
        $pythonNeeded = false;
        foreach ($ordered as $id) {
            if (!empty($all[$id]['python']) && $aiEnabled) {
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
}
