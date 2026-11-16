<?php

declare(strict_types=1);

namespace App\Support\Settings;

use App\Support\Contracts\AuditContract;
use App\Support\Modules\ModuleCatalog;
use App\Support\Tenancy\CompanyContext;
use Illuminate\Database\ConnectionInterface;
use InvalidArgumentException;

/** Typed per-company settings declared by enabled module manifests. */
final class SettingsService
{
    /** @param list<string> $enabled */
    public function __construct(
        private readonly ModuleCatalog $catalog,
        private readonly array $enabled,
        private readonly ConnectionInterface $db,
        private readonly CompanyContext $context,
    ) {}

    public function get(string $module, string $key): mixed
    {
        $declaration = $this->declaration($module, $key);
        $stored = $this->db->table('core_settings')->where(['company_id' => $this->context->companyId(), 'module' => $module, 'key' => $key])->value('value');

        return $stored === null ? $this->validated($declaration, $declaration['default'] ?? null, true) : json_decode((string) $stored, true, 512, JSON_THROW_ON_ERROR);
    }

    /** @return array<string, mixed> */
    public function all(string $module): array
    {
        $values = [];
        foreach ($this->declarations($module) as $key => $declaration) {
            $values[$key] = $this->get($module, $key);
        }

        return $values;
    }

    public function set(string $module, string $key, mixed $value, ?int $actorId = null): void
    {
        $declaration = $this->declaration($module, $key);
        $encoded = json_encode($this->validated($declaration, $value, false), JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION);
        $companyId = $this->context->companyId();
        $this->db->transaction(function () use ($companyId, $module, $key, $encoded, $actorId): void {
            $scope = ['company_id' => $companyId, 'module' => $module, 'key' => $key];
            $existing = $this->db->table('core_settings')->where($scope)->lockForUpdate()->first();
            $now = now();
            if ($existing === null) {
                $id = $this->db->table('core_settings')->insertGetId($scope + ['value' => $encoded, 'updated_by' => $actorId, 'created_at' => $now, 'updated_at' => $now]);
                $before = null;
            } else {
                $id = $existing->id;
                $before = ['value' => json_decode($existing->value, true, 512, JSON_THROW_ON_ERROR)];
                $this->db->table('core_settings')->where('id', $id)->update(['value' => $encoded, 'updated_by' => $actorId, 'updated_at' => $now]);
            }
            app(AuditContract::class)->record($companyId, $actorId, 'core', 'core_settings', $before === null ? 'created' : 'updated', $id, $before, ['module' => $module, 'key' => $key, 'value' => json_decode($encoded, true, 512, JSON_THROW_ON_ERROR)]);
        });
    }

    /** @return array<string, array<string, mixed>> */
    private function declarations(string $module): array
    {
        if (! in_array($module, $this->enabled, true)) {
            throw new InvalidArgumentException('Settings belong to an enabled module only.');
        }
        $declarations = [];
        foreach ($this->catalog->manifests()[$module]['settings'] ?? [] as $declaration) {
            $declarations[$declaration['key']] = $declaration;
        }

        return $declarations;
    }

    /** @return array<string, mixed> */
    private function declaration(string $module, string $key): array
    {
        return $this->declarations($module)[$key] ?? throw new InvalidArgumentException('Undeclared setting: '.$module.'.'.$key);
    }

    /** @param array<string, mixed> $declaration */
    private function validated(array $declaration, mixed $value, bool $isDefault): mixed
    {
        $valid = match ($declaration['type']) {
            'bool' => is_bool($value),
            'int' => is_int($value),
            'float' => (is_int($value) || is_float($value)) && is_finite((float) $value),
            'decimal' => is_string($value) && preg_match('/^-?\d{1,16}(?:\.\d{1,4})?$/D', $value) === 1,
            'string' => is_string($value) && mb_strlen($value) <= 4000,
            'json' => is_array($value),
            default => false,
        };
        if ($value === null && $isDefault) {
            return null;
        }
        if (! $valid) {
            throw new InvalidArgumentException(($isDefault ? 'Manifest default' : 'Value').' does not match setting type '.$declaration['type'].': '.$declaration['key']);
        }

        return $declaration['type'] === 'float' ? (float) $value : $value;
    }
}
