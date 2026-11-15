<?php
declare(strict_types=1);

namespace Modules\AssetRegistry\Services;

use Core\Database\Connection;
use Modules\AssetRegistry\Contracts\AssetRegistryServiceInterface;

final class AssetRegistryService implements AssetRegistryServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function find(int $companyId, array $params = []): mixed
    {
        return $this->db->fetchOne('SELECT * FROM asset_registry__assets WHERE company_id = ? AND id = ?', [$companyId, (int) ($params['id'] ?? 0)]);
    }

    public function tree(int $companyId, array $params = []): mixed
    {
        return $this->db->fetchAll('SELECT * FROM asset_registry__assets WHERE company_id = ? ORDER BY id ASC', [$companyId]);
    }

    public function byQr(int $companyId, array $params = []): mixed
    {
        return $this->db->fetchOne('SELECT * FROM asset_registry__assets WHERE company_id = ? AND qr_code = ?', [$companyId, (string) ($params['code'] ?? '')]);
    }

    public function currentMeter(int $companyId, array $params = []): mixed { return '0.0000'; }
    public function addReading(int $companyId, array $params = []): mixed { return true; }
    public function spareParts(int $companyId, array $params = []): mixed { return []; }
    public function setStatus(int $companyId, array $params = []): mixed { return true; }
    public function lookup(int $companyId, array $params = []): mixed { return $this->tree($companyId, $params); }
}
