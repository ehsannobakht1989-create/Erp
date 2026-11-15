<?php
declare(strict_types=1);

namespace Modules\Bom\Services;

use Core\Database\Connection;
use Core\Support\Money;
use Modules\Bom\Contracts\BomServiceInterface;
use RuntimeException;

final class BomService implements BomServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function findActive(int $companyId, array $params = []): mixed
    {
        $itemId = (int) ($params['item_id'] ?? 0);
        return $this->db->fetchOne('SELECT * FROM bom__boms WHERE company_id = ? AND item_id = ? AND status = ? ORDER BY revision DESC LIMIT 1', [$companyId, $itemId, 'approved']);
    }

    public function explode(int $companyId, array $params = []): mixed
    {
        $itemId = (int) ($params['item_id'] ?? 0);
        $qty = (string) ($params['qty'] ?? '1');
        $visited = (array) ($params['visited'] ?? []);
        if (in_array($itemId, $visited, true)) {
            throw new RuntimeException("BOM_CYCLE: Circular reference detected at item #$itemId");
        }
        $visited[] = $itemId;

        $bom = $this->findActive($companyId, ['item_id' => $itemId]);
        if ($bom === null) {
            return [];
        }
        $lines = $this->db->fetchAll('SELECT * FROM bom__lines WHERE company_id = ? AND bom_id = ?', [$companyId, $bom['id']]);
        $out = [];
        foreach ($lines as $ln) {
            $reqQty = Money::mul($qty, (string) $ln['qty_per']);
            $out[] = ['component_item_id' => (int) $ln['component_item_id'], 'qty' => $reqQty];
        }
        return $out;
    }

    public function whereUsed(int $companyId, array $params = []): mixed { return []; }
    public function compare(int $companyId, array $params = []): mixed { return []; }
    public function costRollupInputs(int $companyId, array $params = []): mixed { return []; }
    public function validate(int $companyId, array $params = []): mixed { return true; }
}
