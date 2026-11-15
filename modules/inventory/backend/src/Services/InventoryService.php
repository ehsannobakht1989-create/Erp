<?php
declare(strict_types=1);

namespace Modules\Inventory\Services;

use Core\Database\Connection;
use Core\Event\EventBus;
use Core\Support\Money;
use Modules\Inventory\Contracts\InventoryServiceInterface;
use RuntimeException;

final class InventoryService implements InventoryServiceInterface
{
    public function __construct(
        private readonly Connection $db,
        private readonly EventBus $bus,
    ) {
    }

    public function getBalance(int $companyId, array $params = []): mixed
    {
        $itemId = (int) ($params['item_id'] ?? 0);
        $whId = (int) ($params['warehouse_id'] ?? 0);
        $row = $this->db->fetchOne(
            'SELECT COALESCE(SUM(qty), 0) AS total FROM inventory__balances WHERE company_id = ? AND item_id = ? AND warehouse_id = ?',
            [$companyId, $itemId, $whId]
        );
        return Money::normalize((string) ($row['total'] ?? '0'));
    }

    public function getAvailable(int $companyId, array $params = []): mixed { return $this->getBalance($companyId, $params); }

    public function hasStock(int $companyId, array $params = []): mixed
    {
        $itemId = (int) ($params['item_id'] ?? 0);
        $row = $this->db->fetchOne('SELECT COALESCE(SUM(qty), 0) AS total FROM inventory__balances WHERE company_id = ? AND item_id = ?', [$companyId, $itemId]);
        return Money::compare((string) ($row['total'] ?? '0'), '0') > 0;
    }

    public function reserve(int $companyId, array $params = []): mixed { return true; }
    public function release(int $companyId, array $params = []): mixed { return true; }

    public function postMovement(int $companyId, array $params = []): mixed
    {
        $itemId = (int) ($params['item_id'] ?? 0);
        $whId = (int) ($params['warehouse_id'] ?? 0);
        $qtyIn = (string) ($params['qty_in'] ?? '0');
        $qtyOut = (string) ($params['qty_out'] ?? '0');
        $idem = (string) ($params['idempotency_key'] ?? bin2hex(random_bytes(8)));
        $lot = (string) ($params['lot_no'] ?? '');

        return $this->db->transaction(function (Connection $conn) use ($companyId, $itemId, $whId, $qtyIn, $qtyOut, $idem, $lot, $params): array {
            $dup = $conn->fetchOne('SELECT id FROM inventory__ledger WHERE company_id = ? AND idempotency_key = ?', [$companyId, $idem]);
            if ($dup !== null) {
                return ['status' => 'idempotent_skip', 'ledger_id' => $dup['id']];
            }

            $balRow = $conn->fetchOne(
                'SELECT id, qty FROM inventory__balances WHERE company_id = ? AND item_id = ? AND warehouse_id = ? AND lot_no = ?',
                [$companyId, $itemId, $whId, $lot]
            );
            $current = (string) ($balRow['qty'] ?? '0');
            $next = Money::sub(Money::add($current, $qtyIn), $qtyOut);
            if (Money::compare($next, '0') < 0) {
                throw new RuntimeException("NEGATIVE_STOCK: Insufficient stock for item #$itemId in warehouse #$whId");
            }

            $now = gmdate('Y-m-d H:i:s');
            $conn->execute(
                'INSERT INTO inventory__ledger (company_id, doc_type, doc_id, item_id, warehouse_id, lot_no, qty_in, qty_out, idempotency_key, posted_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$companyId, (string) ($params['doc_type'] ?? 'receipt'), (string) ($params['doc_id'] ?? '0'), $itemId, $whId, $lot, $qtyIn, $qtyOut, $idem, $now]
            );
            $ledgerId = $conn->lastInsertId();

            if ($balRow === null) {
                $conn->execute(
                    'INSERT INTO inventory__balances (company_id, item_id, warehouse_id, lot_no, qty, updated_at) VALUES (?, ?, ?, ?, ?, ?)',
                    [$companyId, $itemId, $whId, $lot, $next, $now]
                );
            } else {
                $conn->execute('UPDATE inventory__balances SET qty = ?, updated_at = ? WHERE id = ?', [$next, $now, $balRow['id']]);
            }

            $this->bus->emit('inventory.receipt.posted', ['item_id' => $itemId, 'qty' => $next], $companyId, $ledgerId);
            return ['status' => 'posted', 'ledger_id' => $ledgerId, 'balance' => $next];
        });
    }

    public function suggestPicking(int $companyId, array $params = []): mixed { return []; }
    public function getLot(int $companyId, array $params = []): mixed { return null; }
    public function expiringLots(int $companyId, array $params = []): mixed { return []; }
    public function valuationFeed(int $companyId, array $params = []): mixed { return []; }
}
