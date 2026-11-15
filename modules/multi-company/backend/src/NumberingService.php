<?php
declare(strict_types=1);

namespace Modules\MultiCompany;

use Core\Database\Connection;

final class NumberingService
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function next(int $companyId, string $seqKey, string $pattern = 'DOC-{YY}-{####}', int $branchId = 0, string $fiscalYear = '1403'): string
    {
        return (string) $this->db->transaction(function (Connection $conn) use ($companyId, $seqKey, $pattern, $branchId, $fiscalYear): string {
            $now = gmdate('Y-m-d H:i:s');
            $row = $conn->fetchOne(
                'SELECT id, pattern, last_value FROM multi_company__sequences WHERE company_id = ? AND branch_id = ? AND fiscal_year = ? AND seq_key = ?',
                [$companyId, $branchId, $fiscalYear, $seqKey]
            );
            if ($row === null) {
                $nextVal = 1;
                $conn->execute(
                    'INSERT INTO multi_company__sequences (company_id, branch_id, fiscal_year, seq_key, pattern, last_value, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                    [$companyId, $branchId, $fiscalYear, $seqKey, $pattern, $nextVal, $now, $now]
                );
                $pat = $pattern;
            } else {
                $nextVal = (int) $row['last_value'] + 1;
                $pat = (string) $row['pattern'];
                $conn->execute(
                    'UPDATE multi_company__sequences SET last_value = ?, updated_at = ? WHERE id = ?',
                    [$nextVal, $now, $row['id']]
                );
            }

            $out = str_replace('{YY}', substr($fiscalYear, -2), $pat);
            $out = preg_replace_callback('/\{(#+)\}/', fn(array $m): string => str_pad((string) $nextVal, strlen($m[1]), '0', STR_PAD_LEFT), $out);
            return (string) $out;
        });
    }
}
