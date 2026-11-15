<?php
declare(strict_types=1);

namespace Core\Database;

use RuntimeException;

final class QueryBuilder
{
    private string $table = '';
    /** @var array<int, string> */
    private array $columns = ['*'];
    /** @var array<int, string> */
    private array $joins = [];
    /** @var array<int, string> */
    private array $wheres = [];
    /** @var array<int, mixed> */
    private array $bindings = [];
    private ?string $orderBy = null;
    private ?int $limit = null;
    private ?int $offset = null;

    public function __construct(
        private readonly Connection $db,
        private readonly ?string $moduleScope = null,
        private readonly bool $enforceTableGuard = true,
    ) {
    }

    public function table(string $table): self
    {
        $this->guardTable($table);
        $clone = clone $this;
        $clone->table = $table;
        return $clone;
    }

    /** @param array<int, string> $columns */
    public function select(array $columns = ['*']): self
    {
        $clone = clone $this;
        $clone->columns = $columns;
        return $clone;
    }

    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self
    {
        $this->guardTable($table);
        $clone = clone $this;
        $clone->joins[] = sprintf('%s JOIN %s ON %s %s %s', strtoupper($type), $table, $first, $operator, $second);
        return $clone;
    }

    public function where(string $column, string $operator, mixed $value): self
    {
        $clone = clone $this;
        $clone->wheres[] = "$column $operator ?";
        $clone->bindings[] = $value;
        return $clone;
    }

    public function forCompany(int $companyId): self
    {
        return $this->where('company_id', '=', $companyId);
    }

    public function whereNotDeleted(): self
    {
        $clone = clone $this;
        $clone->wheres[] = 'deleted_at IS NULL';
        return $clone;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $dir = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $clone = clone $this;
        $clone->orderBy = "$column $dir";
        return $clone;
    }

    public function limit(int $limit, int $offset = 0): self
    {
        $clone = clone $this;
        $clone->limit = $limit;
        $clone->offset = $offset;
        return $clone;
    }

    /** @return array<int, array<string, mixed>> */
    public function get(): array
    {
        $sql = $this->compileSelect();
        return $this->db->fetchAll($sql, $this->bindings);
    }

    /** @return array<string, mixed>|null */
    public function first(): ?array
    {
        return $this->limit(1)->get()[0] ?? null;
    }

    /** @return array{items: array<int, array<string, mixed>>, total: int, page: int, per_page: int} */
    public function paginate(int $page = 1, int $perPage = 25): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(200, $perPage));
        $countSql = 'SELECT COUNT(*) AS cnt FROM ' . $this->table
            . ($this->joins ? ' ' . implode(' ', $this->joins) : '')
            . ($this->wheres ? ' WHERE ' . implode(' AND ', $this->wheres) : '');
        $totalRow = $this->db->fetchOne($countSql, $this->bindings);
        $total = (int) ($totalRow['cnt'] ?? 0);
        $items = $this->limit($perPage, ($page - 1) * $perPage)->get();
        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    /** @param array<string, mixed> $data */
    public function insert(array $data): string
    {
        $cols = array_keys($data);
        $placeholders = implode(', ', array_fill(0, count($cols), '?'));
        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->table, implode(', ', $cols), $placeholders);
        $this->db->execute($sql, array_values($data));
        return $this->db->lastInsertId();
    }

    /** @param array<string, mixed> $data */
    public function update(array $data): int
    {
        $sets = [];
        $params = [];
        foreach ($data as $k => $v) {
            $sets[] = "$k = ?";
            $params[] = $v;
        }
        $sql = sprintf('UPDATE %s SET %s', $this->table, implode(', ', $sets));
        if ($this->wheres) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
        return $this->db->execute($sql, array_merge($params, $this->bindings));
    }

    public function delete(): int
    {
        $sql = 'DELETE FROM ' . $this->table;
        if ($this->wheres) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
        return $this->db->execute($sql, $this->bindings);
    }

    public function softDelete(): int
    {
        return $this->update(['deleted_at' => gmdate('Y-m-d H:i:s')]);
    }

    private function compileSelect(): string
    {
        $sql = 'SELECT ' . implode(', ', $this->columns) . ' FROM ' . $this->table;
        if ($this->joins) {
            $sql .= ' ' . implode(' ', $this->joins);
        }
        if ($this->wheres) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
        if ($this->orderBy !== null) {
            $sql .= ' ORDER BY ' . $this->orderBy;
        }
        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
            if ($this->offset !== null && $this->offset > 0) {
                $sql .= ' OFFSET ' . $this->offset;
            }
        }
        return $sql;
    }

    private function guardTable(string $table): void
    {
        if (!$this->enforceTableGuard || $this->moduleScope === null) {
            return;
        }
        $prefix = str_replace('-', '_', $this->moduleScope) . '__';
        $kebabPrefix = $this->moduleScope . '__';
        if (!str_starts_with($table, $prefix) && !str_starts_with($table, $kebabPrefix)) {
            throw new RuntimeException(
                "Table-prefix violation: module '{$this->moduleScope}' cannot access table '$table' (must start with '$prefix')."
            );
        }
    }
}
