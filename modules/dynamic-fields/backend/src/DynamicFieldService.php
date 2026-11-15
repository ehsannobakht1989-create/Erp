<?php
declare(strict_types=1);

namespace Modules\DynamicFields;

use Core\Contracts\DynamicFieldContract;
use Core\Database\Connection;

final class DynamicFieldService implements DynamicFieldContract
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function fieldsFor(int $companyId, string $entity): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM dynamic_fields__fields WHERE company_id = ? AND entity = ? ORDER BY sort_order ASC, id ASC',
            [$companyId, $entity]
        );
    }

    /** @param array<string, mixed> $values */
    public function saveValues(int $companyId, string $entity, int|string $entityId, array $values): void
    {
        $now = gmdate('Y-m-d H:i:s');
        foreach ($values as $key => $val) {
            $existing = $this->db->fetchOne(
                'SELECT id FROM dynamic_fields__values WHERE company_id = ? AND entity = ? AND entity_id = ? AND field_key = ?',
                [$companyId, $entity, (string) $entityId, (string) $key]
            );
            $num = is_numeric($val) ? (string) $val : null;
            $txt = is_scalar($val) ? (string) $val : (string) json_encode($val, JSON_UNESCAPED_UNICODE);
            if ($existing !== null) {
                $this->db->execute(
                    'UPDATE dynamic_fields__values SET val_text = ?, val_num = ?, updated_at = ? WHERE id = ?',
                    [$txt, $num, $now, $existing['id']]
                );
            } else {
                $this->db->execute(
                    'INSERT INTO dynamic_fields__values (company_id, entity, entity_id, field_key, val_text, val_num, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
                    [$companyId, $entity, (string) $entityId, (string) $key, $txt, $num, $now]
                );
            }
        }
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['dynamic-fields.field.created'];
    }
}
