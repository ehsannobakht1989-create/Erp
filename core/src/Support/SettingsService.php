<?php
declare(strict_types=1);

namespace Core\Support;

use Core\Database\Connection;
use Core\Module\ModuleRegistry;

final class SettingsService
{
    public function __construct(
        private readonly ModuleRegistry $registry,
        private readonly ?Connection $db = null,
        private readonly ?FileCache $cache = null,
    ) {
    }

    public function get(int $companyId, string $moduleId, string $key): mixed
    {
        $cacheKey = "settings:$companyId:$moduleId:$key";
        if ($this->cache !== null) {
            $cached = $this->cache->get($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }

        $manifest = $this->registry->get($moduleId);
        $def = null;
        foreach ($manifest['settings'] ?? [] as $s) {
            if (($s['key'] ?? '') === $key) {
                $def = $s;
                break;
            }
        }
        $type = $def['type'] ?? 'string';
        $default = $def['default'] ?? null;

        if ($this->db !== null) {
            $row = $this->db->fetchOne(
                'SELECT setting_value FROM core__settings WHERE company_id = ? AND module_id = ? AND setting_key = ?',
                [$companyId, $moduleId, $key]
            );
            if ($row !== null && $row['setting_value'] !== null) {
                $val = $this->cast((string) $row['setting_value'], $type);
                $this->cache?->set($cacheKey, $val, 300);
                return $val;
            }
        }

        return $default;
    }

    public function set(int $companyId, string $moduleId, string $key, mixed $value): void
    {
        if ($this->db === null) {
            return;
        }
        $encoded = is_bool($value) ? ($value ? '1' : '0') : (is_array($value) ? (string) json_encode($value) : (string) $value);
        $now = gmdate('Y-m-d H:i:s');
        $existing = $this->db->fetchOne(
            'SELECT id FROM core__settings WHERE company_id = ? AND module_id = ? AND setting_key = ?',
            [$companyId, $moduleId, $key]
        );
        if ($existing !== null) {
            $this->db->execute(
                'UPDATE core__settings SET setting_value = ?, updated_at = ? WHERE id = ?',
                [$encoded, $now, $existing['id']]
            );
        } else {
            $this->db->execute(
                'INSERT INTO core__settings (company_id, module_id, setting_key, setting_value, updated_at) VALUES (?, ?, ?, ?, ?)',
                [$companyId, $moduleId, $key, $encoded, $now]
            );
        }
        $this->cache?->delete("settings:$companyId:$moduleId:$key");
    }

    private function cast(string $raw, string $type): mixed
    {
        return match ($type) {
            'bool' => in_array(strtolower($raw), ['1', 'true', 'yes'], true),
            'int' => (int) $raw,
            'float' => (float) $raw,
            'json' => json_decode($raw, true),
            default => $raw,
        };
    }
}
