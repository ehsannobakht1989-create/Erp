<?php

declare(strict_types=1);

namespace App\Support\Modules;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionResolverInterface;
use RuntimeException;

/**
 * Deployment-wide activation, not a per-company authorization mechanism.
 * The connection is resolved per call so purged/reconnected connections are never reused.
 */
final class ModuleState
{
    /** @param list<string> $configured */
    public function __construct(
        private readonly ModuleCatalog $catalog,
        private readonly ConnectionResolverInterface $db,
        private readonly array $configured,
        private readonly string $modulesPath,
    ) {}

    private function connection(): Connection
    {
        $connection = $this->db->connection();
        if (! $connection instanceof Connection) {
            throw new RuntimeException('Module state requires a database connection.');
        }

        return $connection;
    }

    /** @return list<string> */
    public function enabled(): array
    {
        $enabled = array_fill_keys($this->catalog->ordered($this->configured), true);
        if ($this->hasOverrides()) {
            foreach ($this->connection()->table('core_modules')->orderBy('id')->get() as $row) {
                if (! array_key_exists($row->id, $this->catalog->manifests())) {
                    throw new RuntimeException('Unknown persisted module: '.$row->id);
                }
                if (! in_array($row->enabled, [0, 1, '0', '1', false, true], true)) {
                    throw new RuntimeException('Invalid persisted module state: '.$row->id);
                }
                if ((bool) $row->enabled) {
                    $enabled[$row->id] = true;
                } else {
                    unset($enabled[$row->id]);
                }
            }
        }

        return $this->catalog->ordered(array_keys($enabled));
    }

    /** Validate the entire target set, then persist one override atomically. */
    public function set(string $id, bool $enabled): void
    {
        if (! isset($this->catalog->manifests()[$id])) {
            throw new RuntimeException('Unknown module: '.$id);
        }
        if (! $this->hasOverrides()) {
            throw new RuntimeException('Run core migrations before changing module state.');
        }
        $this->connection()->transaction(function () use ($id, $enabled): void {
            // One shared row serializes all administrative state changes on supported DBs.
            $this->connection()->table('core_module_locks')->where('id', 1)->lockForUpdate()->firstOrFail();
            $target = array_fill_keys($this->enabled(), true);
            if ($enabled) {
                $target[$id] = true;
            } else {
                unset($target[$id]);
            }
            $ordered = $this->catalog->ordered(array_keys($target));
            foreach ($ordered as $module) {
                if (! is_file($this->modulesPath.'/'.$module.'/Providers/ModuleServiceProvider.php')) {
                    throw new RuntimeException('Enabled module '.$module.' has no Laravel provider.');
                }
            }
            $this->connection()->table('core_modules')->updateOrInsert(['id' => $id], ['enabled' => $enabled]);
        });
    }

    private function hasOverrides(): bool
    {
        // Before installation a SQLite file may not exist; do not create it by probing.
        if ($this->connection()->getDriverName() === 'sqlite') {
            $database = $this->connection()->getDatabaseName();
            if ($database !== ':memory:' && ! is_file($database)) {
                return false;
            }
        }

        return $this->connection()->getSchemaBuilder()->hasTable('core_modules');
    }
}
