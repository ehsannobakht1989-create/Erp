<?php
declare(strict_types=1);

namespace Modules\Pricing\Services;

use Core\Database\Connection;
use Modules\Pricing\Contracts\PricingServiceInterface;

final class PricingService implements PricingServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function resolve(int $companyId, array $params = []): mixed
    {
        $itemId = (int) ($params['item_id'] ?? 0);
        $qty = (string) ($params['qty'] ?? '1');
        $row = $this->db->fetchOne(
            'SELECT l.code AS list_code, ln.price, ln.discount_pct
             FROM pricing__lines ln
             JOIN pricing__lists l ON l.id = ln.list_id
             WHERE ln.company_id = ? AND ln.item_id = ? AND l.status = ? AND ln.min_qty <= ?
             ORDER BY l.priority ASC, ln.min_qty DESC LIMIT 1',
            [$companyId, $itemId, 'active', $qty]
        );
        return $row ?? ['list_code' => null, 'price' => '0.0000', 'discount_pct' => '0.0000'];
    }

    public function resolveBulk(int $companyId, array $params = []): mixed { return []; }
    public function explain(int $companyId, array $params = []): mixed { return []; }
    public function activeLists(int $companyId, array $params = []): mixed
    {
        return $this->db->fetchAll('SELECT * FROM pricing__lists WHERE company_id = ? AND status = ?', [$companyId, 'active']);
    }
}
