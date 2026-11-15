<?php
declare(strict_types=1);

namespace Modules\PartyMaster\Services;

use Core\Contracts\AuditContract;
use Core\Database\Connection;
use Core\Event\EventBus;
use Modules\PartyMaster\Contracts\PartyMasterServiceInterface;
use Modules\PartyMaster\Repositories\PartyRepository;
use RuntimeException;

final class PartyMasterService implements PartyMasterServiceInterface
{
    private PartyRepository $repo;

    public function __construct(
        private readonly Connection $db,
        AuditContract $audit,
        private readonly EventBus $bus,
    ) {
        $this->repo = new PartyRepository($db, $audit);
    }

    public function find(int $companyId, array $params = []): mixed
    {
        $id = (int) ($params['id'] ?? 0);
        return $this->repo->findById($companyId, $id);
    }

    public function findByNationalId(int $companyId, array $params = []): mixed
    {
        $nid = (string) ($params['national_id'] ?? '');
        return $this->db->fetchOne(
            'SELECT * FROM party_master__parties WHERE company_id = ? AND national_id = ? AND deleted_at IS NULL',
            [$companyId, $nid]
        );
    }

    public function lookup(int $companyId, array $params = []): mixed
    {
        $q = (string) ($params['q'] ?? '');
        return $this->db->fetchAll(
            'SELECT id, code, name_fa, party_type FROM party_master__parties WHERE company_id = ? AND status = ? AND deleted_at IS NULL AND (name_fa LIKE ? OR code LIKE ?) LIMIT 20',
            [$companyId, 'active', "%$q%", "%$q%"]
        );
    }

    public function isActive(int $companyId, array $params = []): mixed
    {
        $p = $this->find($companyId, $params);
        return is_array($p) && ($p['status'] ?? '') === 'active';
    }

    public function getAddresses(int $companyId, array $params = []): mixed { return []; }
    public function getDefaultBank(int $companyId, array $params = []): mixed { return null; }
    public function getCreditLimit(int $companyId, array $params = []): mixed
    {
        $p = $this->find($companyId, $params);
        return $p['credit_limit'] ?? '0.0000';
    }
    public function getPaymentTerm(int $companyId, array $params = []): mixed { return null; }
    public function validateIds(int $companyId, array $params = []): mixed { return true; }

    public function markUsed(int $companyId, array $params = []): mixed
    {
        $id = (int) ($params['id'] ?? 0);
        $this->repo->updateById($companyId, null, $id, ['is_used' => 1]);
        return true;
    }

    /** @param array<string, mixed> $data */
    public function create(int $companyId, ?int $actorId, array $data): array
    {
        $code = trim((string) ($data['code'] ?? ''));
        $nameFa = trim((string) ($data['name_fa'] ?? ''));
        if ($code === '' || $nameFa === '') {
            throw new RuntimeException('VALIDATION_ERROR: code and name_fa are required');
        }
        $id = (int) $this->repo->insert($companyId, $actorId, [
            'code' => $code,
            'party_type' => $data['party_type'] ?? 'customer',
            'legal_kind' => $data['legal_kind'] ?? 'legal',
            'name_fa' => $nameFa,
            'national_id' => $data['national_id'] ?? null,
            'credit_limit' => $data['credit_limit'] ?? '0.0000',
            'status' => ($data['party_type'] ?? 'customer') === 'customer' ? 'active' : 'draft',
            'is_used' => 0,
        ]);
        $this->bus->emit('party-master.party.created', ['id' => $id, 'code' => $code], $companyId, $id, $actorId);
        return $this->find($companyId, ['id' => $id]) ?? [];
    }
}
