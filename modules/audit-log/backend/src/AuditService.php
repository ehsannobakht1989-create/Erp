<?php
declare(strict_types=1);

namespace Modules\AuditLog;

use Core\Contracts\AuditContract;
use Core\Database\Connection;

final class AuditService implements AuditContract
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function record(
        int $companyId,
        ?int $actorId,
        string $module,
        string $table,
        string $action,
        int|string $recordId,
        ?array $before = null,
        ?array $after = null,
    ): void {
        $last = $this->db->fetchOne('SELECT entry_hash FROM audit_log__entries ORDER BY id DESC LIMIT 1');
        $prevHash = $last['entry_hash'] ?? str_repeat('0', 64);
        $now = gmdate('Y-m-d H:i:s');
        $beforeJson = $before !== null ? (string) json_encode($before, JSON_UNESCAPED_UNICODE) : null;
        $afterJson = $after !== null ? (string) json_encode($after, JSON_UNESCAPED_UNICODE) : null;
        $entryHash = hash('sha256', "$prevHash|$companyId|$actorId|$module|$table|$action|$recordId|$beforeJson|$afterJson|$now");

        $this->db->execute(
            'INSERT INTO audit_log__entries (company_id, actor_id, module_id, table_name, action, record_id, before_json, after_json, prev_hash, entry_hash, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [$companyId, $actorId, $module, $table, $action, (string) $recordId, $beforeJson, $afterJson, $prevHash, $entryHash, $now]
        );
    }

    /** @param array<string, mixed> $filters
     *  @return array<int, array<string, mixed>> */
    public function query(int $companyId, array $filters = []): array
    {
        $sql = 'SELECT * FROM audit_log__entries WHERE company_id = ?';
        $params = [$companyId];
        if (!empty($filters['entity_type'])) {
            $sql .= ' AND table_name = ?';
            $params[] = (string) $filters['entity_type'];
        }
        if (!empty($filters['id'])) {
            $sql .= ' AND id = ?';
            $params[] = (int) $filters['id'];
        }
        $sql .= ' ORDER BY id DESC';
        return $this->db->fetchAll($sql, $params);
    }

    public function verifyChain(): bool
    {
        $rows = $this->db->fetchAll('SELECT * FROM audit_log__entries ORDER BY id ASC');
        $expectedPrev = str_repeat('0', 64);
        foreach ($rows as $row) {
            if ($row['prev_hash'] !== $expectedPrev) {
                return false;
            }
            $expectedPrev = $row['entry_hash'];
        }
        return true;
    }
}
