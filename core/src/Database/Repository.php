<?php
declare(strict_types=1);

namespace Core\Database;

use Core\Contracts\AuditContract;

abstract class Repository
{
    public function __construct(
        protected readonly Connection $db,
        protected readonly AuditContract $audit,
        protected readonly string $moduleId,
        protected readonly string $table,
        protected readonly bool $enforceTableGuard = true,
    ) {
    }

    protected function qb(): QueryBuilder
    {
        return (new QueryBuilder($this->db, $this->moduleId, $this->enforceTableGuard))->table($this->table);
    }

    /** @param array<string, mixed> $data */
    public function insert(int $companyId, ?int $actorId, array $data): string
    {
        $now = gmdate('Y-m-d H:i:s');
        $data['company_id'] ??= $companyId;
        $data['created_at'] ??= $now;
        $data['updated_at'] ??= $now;

        $id = $this->qb()->insert($data);
        $this->audit->record($companyId, $actorId, $this->moduleId, $this->table, 'insert', $id, null, $data);
        return $id;
    }

    /** @param array<string, mixed> $data */
    public function updateById(int $companyId, ?int $actorId, int|string $id, array $data): int
    {
        $before = $this->qb()->forCompany($companyId)->where('id', '=', $id)->first();
        $data['updated_at'] ??= gmdate('Y-m-d H:i:s');
        $affected = $this->qb()->forCompany($companyId)->where('id', '=', $id)->update($data);
        if ($affected > 0) {
            $after = $this->qb()->forCompany($companyId)->where('id', '=', $id)->first();
            $this->audit->record($companyId, $actorId, $this->moduleId, $this->table, 'update', $id, $before, $after);
        }
        return $affected;
    }

    public function deleteById(int $companyId, ?int $actorId, int|string $id, bool $soft = true): int
    {
        $before = $this->qb()->forCompany($companyId)->where('id', '=', $id)->first();
        $affected = $soft
            ? $this->qb()->forCompany($companyId)->where('id', '=', $id)->softDelete()
            : $this->qb()->forCompany($companyId)->where('id', '=', $id)->delete();
        if ($affected > 0) {
            $this->audit->record($companyId, $actorId, $this->moduleId, $this->table, 'delete', $id, $before, null);
        }
        return $affected;
    }
}
