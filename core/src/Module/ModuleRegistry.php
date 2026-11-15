<?php
declare(strict_types=1);

namespace Core\Module;

final class ModuleRegistry
{
    /** @var array<string, array<string, mixed>> */
    private array $all;

    /** @var array<int, string> */
    private array $enabledIds;

    /**
     * @param array<string, array<string, mixed>> $allSorted
     * @param array<int, string> $enabledIds
     */
    public function __construct(array $allSorted, array $enabledIds = [])
    {
        $this->all = $allSorted;
        $this->enabledIds = array_values(array_unique($enabledIds));
    }

    /** @return array<string, array<string, mixed>> */
    public function all(): array
    {
        return $this->all;
    }

    /** @return array<string, array<string, mixed>> */
    public function enabled(): array
    {
        $out = [];
        foreach ($this->all as $id => $manifest) {
            if (in_array($id, $this->enabledIds, true)) {
                $out[$id] = $manifest;
            }
        }
        return $out;
    }

    public function isEnabled(string $id): bool
    {
        return in_array($id, $this->enabledIds, true) && isset($this->all[$id]);
    }

    /** @return array<string, mixed>|null */
    public function get(string $id): ?array
    {
        return $this->all[$id] ?? null;
    }

    /** @return array<int, array<string, mixed>> */
    public function menus(): array
    {
        $menus = [];
        foreach ($this->enabled() as $manifest) {
            foreach ($manifest['menu'] ?? [] as $item) {
                $menus[] = $item;
            }
        }
        return $menus;
    }

    /** @return array<int, string> */
    public function permissions(): array
    {
        $perms = [];
        foreach ($this->enabled() as $manifest) {
            foreach ($manifest['permissions'] ?? [] as $p) {
                $perms[] = $p;
            }
        }
        return array_values(array_unique($perms));
    }
}
