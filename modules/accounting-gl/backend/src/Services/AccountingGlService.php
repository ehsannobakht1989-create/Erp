<?php
declare(strict_types=1);

namespace Modules\AccountingGl\Services;

use Core\Database\Connection;
use Core\Event\EventBus;
use Core\Support\Money;
use Modules\AccountingGl\Contracts\AccountingGlServiceInterface;
use RuntimeException;

final class AccountingGlService implements AccountingGlServiceInterface
{
    public function __construct(
        private readonly Connection $db,
        private readonly EventBus $bus,
    ) {
    }

    public function requestJournal(int $companyId, array $params = []): mixed
    {
        $lines = (array) ($params['lines'] ?? []);
        if (empty($lines)) {
            throw new RuntimeException('VALIDATION_ERROR: Journal must have lines');
        }
        $totalDebit = '0.0000';
        $totalCredit = '0.0000';
        foreach ($lines as $l) {
            $totalDebit = Money::add($totalDebit, (string) ($l['debit'] ?? '0'));
            $totalCredit = Money::add($totalCredit, (string) ($l['credit'] ?? '0'));
        }
        if (Money::compare($totalDebit, $totalCredit) !== 0) {
            throw new RuntimeException("UNBALANCED_JOURNAL: Debit ($totalDebit) != Credit ($totalCredit)");
        }

        return $this->db->transaction(function (Connection $conn) use ($companyId, $lines, $params): array {
            $now = gmdate('Y-m-d H:i:s');
            $no = (string) ($params['journal_no'] ?? ('J-' . time() . '-' . rand(100, 999)));
            $conn->execute(
                'INSERT INTO accounting_gl__journals (company_id, journal_no, journal_date, status, source_ref, description, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$companyId, $no, (string) ($params['date'] ?? gmdate('Y-m-d')), 'permanent', $params['source_ref'] ?? null, $params['description'] ?? null, $now]
            );
            $jid = (int) $conn->lastInsertId();
            foreach ($lines as $l) {
                $conn->execute(
                    'INSERT INTO accounting_gl__journal_lines (company_id, journal_id, account_id, debit, credit, description) VALUES (?, ?, ?, ?, ?, ?)',
                    [$companyId, $jid, (int) $l['account_id'], Money::normalize((string) ($l['debit'] ?? '0')), Money::normalize((string) ($l['credit'] ?? '0')), $l['description'] ?? null]
                );
            }
            $this->bus->emit('accounting-gl.journal.posted', ['journal_id' => $jid], $companyId, $jid);
            return ['journal_id' => $jid, 'journal_no' => $no, 'status' => 'permanent'];
        });
    }

    public function post(int $companyId, array $params = []): mixed { return true; }
    public function accountBalance(int $companyId, array $params = []): mixed { return '0.0000'; }
    public function trialBalance(int $companyId, array $params = []): mixed
    {
        return $this->db->fetchAll(
            'SELECT account_id, SUM(debit) AS total_debit, SUM(credit) AS total_credit FROM accounting_gl__journal_lines WHERE company_id = ? GROUP BY account_id',
            [$companyId]
        );
    }
    public function isPeriodOpen(int $companyId, array $params = []): mixed { return true; }
    public function resolveAccount(int $companyId, array $params = []): mixed { return null; }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['accounting-gl.journal.reversed', 'accounting-gl.period.closed'];
    }
}
