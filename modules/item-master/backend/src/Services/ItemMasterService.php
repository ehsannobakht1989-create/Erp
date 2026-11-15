<?php
declare(strict_types=1);

namespace Modules\ItemMaster\Services;

use Core\Contracts\AuditContract;
use Core\Contracts\WorkflowContract;
use Core\Database\Connection;
use Core\Event\EventBus;
use Modules\ItemMaster\Contracts\ItemMasterServiceInterface;
use Modules\ItemMaster\Repositories\ItemRepository;
use RuntimeException;

final class ItemMasterService implements ItemMasterServiceInterface
{
    private ItemRepository $repo;

    public function __construct(
        private readonly Connection $db,
        AuditContract $audit,
        private readonly EventBus $bus,
        private readonly ?WorkflowContract $workflow = null,
    ) {
        $this->repo = new ItemRepository($db, $audit);
    }

    public function find(int $companyId, int $id): ?array
    {
        return $this->repo->findById($companyId, $id);
    }

    public function findByCode(int $companyId, string $code): ?array
    {
        return $this->repo->findByCode($companyId, $code);
    }

    public function search(int $companyId, string $query = '', array $filters = [], int $limit = 25): array
    {
        $sql = 'SELECT * FROM item_master__items WHERE company_id = ? AND deleted_at IS NULL';
        $params = [$companyId];
        if ($query !== '') {
            $norm = TextNormalizer::normalize($query);
            $sql .= ' AND (normalized_name LIKE ? OR code LIKE ?)';
            $params[] = "%$norm%";
            $params[] = "%$query%";
        }
        if (!empty($filters['status'])) {
            $sql .= ' AND status = ?';
            $params[] = $filters['status'];
        }
        $sql .= ' ORDER BY id DESC LIMIT ' . max(1, min(200, $limit));
        return $this->db->fetchAll($sql, $params);
    }

    public function lookup(int $companyId, string $query = '', int $limit = 20): array
    {
        return $this->search($companyId, $query, ['status' => 'active'], $limit);
    }

    public function isActive(int $companyId, int $id): bool
    {
        $item = $this->find($companyId, $id);
        return $item !== null && ($item['status'] ?? '') === 'active';
    }

    /** @param array<string, mixed> $data */
    public function create(int $companyId, ?int $actorId, array $data): array
    {
        $code = trim((string) ($data['code'] ?? ''));
        $nameFa = trim((string) ($data['name_fa'] ?? ''));
        if ($code === '' || $nameFa === '') {
            throw new RuntimeException('VALIDATION_ERROR: code and name_fa are required');
        }
        if ($this->findByCode($companyId, $code) !== null) {
            throw new RuntimeException("CONFLICT: Item code '$code' already exists");
        }

        $norm = TextNormalizer::normalize($nameFa);
        $similar = $this->db->fetchAll(
            'SELECT id, code, name_fa FROM item_master__items WHERE company_id = ? AND normalized_name = ? AND deleted_at IS NULL LIMIT 5',
            [$companyId, $norm]
        );
        if (!empty($similar) && empty($data['override_duplicate'])) {
            return ['ok' => false, 'warning' => 'DUPLICATE_NAME', 'similar' => $similar];
        }

        $id = (int) $this->repo->insert($companyId, $actorId, [
            'code' => $code,
            'name_fa' => $nameFa,
            'name_en' => $data['name_en'] ?? null,
            'normalized_name' => $norm,
            'item_type' => $data['item_type'] ?? 'raw_material',
            'category_id' => $data['category_id'] ?? null,
            'base_uom_id' => $data['base_uom_id'] ?? 'PCS',
            'status' => 'draft',
            'is_used' => 0,
        ]);

        $this->bus->emit('item-master.item.created', ['id' => $id, 'code' => $code], $companyId, $id, $actorId);
        return ['ok' => true, 'item' => $this->find($companyId, $id)];
    }

    /** @param array<string, mixed> $data */
    public function update(int $companyId, ?int $actorId, int $id, array $data): array
    {
        $item = $this->find($companyId, $id);
        if ($item === null) {
            throw new RuntimeException('NOT_FOUND: Item not found');
        }
        if (!empty($item['is_used']) && isset($data['code']) && $data['code'] !== $item['code']) {
            throw new RuntimeException('CONFLICT: Item code is immutable after use');
        }
        if (isset($data['name_fa'])) {
            $data['normalized_name'] = TextNormalizer::normalize((string) $data['name_fa']);
        }
        $this->repo->updateById($companyId, $actorId, $id, $data);
        $this->bus->emit('item-master.item.updated', ['id' => $id], $companyId, $id, $actorId);
        return $this->find($companyId, $id) ?? [];
    }

    public function activate(int $companyId, ?int $actorId, int $id): array
    {
        $item = $this->find($companyId, $id);
        if ($item === null) {
            throw new RuntimeException('NOT_FOUND: Item not found');
        }
        $this->workflow?->transition($companyId, 'item_activation', $id, 'activate', ['to_state' => 'active', 'actor_id' => $actorId]);
        $this->repo->updateById($companyId, $actorId, $id, ['status' => 'active']);
        $this->bus->emit('item-master.item.activated', ['id' => $id], $companyId, $id, $actorId);
        return $this->find($companyId, $id) ?? [];
    }

    public function convert(int $companyId, int $itemId, string $qty, string $fromUom, string $toUom, int $scale = 4): string
    {
        if ($fromUom === $toUom) {
            return bcadd($qty, '0', $scale);
        }
        $row = $this->db->fetchOne(
            'SELECT factor FROM item_master__uom_conversions WHERE company_id = ? AND item_id = ? AND from_uom = ? AND to_uom = ?',
            [$companyId, $itemId, $fromUom, $toUom]
        );
        if ($row !== null) {
            return bcmul($qty, (string) $row['factor'], $scale);
        }
        $rev = $this->db->fetchOne(
            'SELECT factor FROM item_master__uom_conversions WHERE company_id = ? AND item_id = ? AND from_uom = ? AND to_uom = ?',
            [$companyId, $itemId, $toUom, $fromUom]
        );
        if ($rev !== null && bccomp((string) $rev['factor'], '0', 6) !== 0) {
            return bcdiv($qty, (string) $rev['factor'], $scale);
        }
        throw new RuntimeException("No UoM conversion from '$fromUom' to '$toUom' for item #$itemId");
    }

    public function markUsed(int $companyId, int $id, string $byModule): void
    {
        $this->repo->updateById($companyId, null, $id, ['is_used' => 1]);
    }
}
