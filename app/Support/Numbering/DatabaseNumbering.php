<?php

declare(strict_types=1);

namespace App\Support\Numbering;

use App\Support\Contracts\AuditContract;
use App\Support\Contracts\NumberingContract;
use App\Support\Tenancy\CompanyContext;
use App\Support\Values\Jalali;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Database\ConnectionInterface;
use InvalidArgumentException;
use LogicException;

/** Gap-free when called inside the business transaction: rollback releases the number. */
final class DatabaseNumbering implements NumberingContract
{
    public const DEFAULT_PATTERN = '{jy}-{seq:6}';

    public function __construct(private readonly ConnectionInterface $db, private readonly CompanyContext $context) {}

    public function next(int $companyId, string $sequence, ?int $branchId = null, ?int $fiscalYearId = null, ?DateTimeInterface $at = null): string
    {
        $this->guard($companyId, $sequence);
        if ($this->db->transactionLevel() < 1) {
            throw new LogicException('Numbers must be allocated inside the business transaction.');
        }
        foreach ([$branchId, $fiscalYearId] as $id) {
            if ($id !== null && $id < 1) {
                throw new InvalidArgumentException('Branch and fiscal year IDs must be positive.');
            }
        }
        $pattern = (string) ($this->db->table('core_number_formats')->where(['company_id' => $companyId, 'sequence' => $sequence])->value('pattern') ?? self::DEFAULT_PATTERN);
        $local = CarbonImmutable::instance($at ?? now())->setTimezone((string) config('erp.display_timezone', 'Asia/Tehran'));
        [$jy, $jm, $jd] = Jalali::fromGregorian((int) $local->format('Y'), (int) $local->format('n'), (int) $local->format('j'));
        $tokens = ['jy' => sprintf('%04d', $jy), 'jm' => sprintf('%02d', $jm), 'jd' => sprintf('%02d', $jd), 'gy' => $local->format('Y'), 'gm' => $local->format('m'), 'gd' => $local->format('d'), 'branch' => (string) ($branchId ?? 0)];
        $period = match (true) {
            str_contains($pattern, '{jd}') => $tokens['jy'].$tokens['jm'].$tokens['jd'],
            str_contains($pattern, '{jm}') => $tokens['jy'].$tokens['jm'],
            str_contains($pattern, '{jy}') => $tokens['jy'],
            str_contains($pattern, '{gd}') => 'g'.$local->format('Ymd'),
            str_contains($pattern, '{gm}') => 'g'.$local->format('Ym'),
            str_contains($pattern, '{gy}') => 'g'.$tokens['gy'],
            default => '',
        };
        $scope = ['company_id' => $companyId, 'sequence' => $sequence, 'branch_id' => $branchId ?? 0, 'fiscal_year_id' => $fiscalYearId ?? 0, 'period' => $period];
        $now = now();
        $this->db->table('core_number_sequences')->insertOrIgnore($scope + ['next_value' => 1, 'created_at' => $now, 'updated_at' => $now]);
        $row = $this->db->table('core_number_sequences')->where($scope)->lockForUpdate()->first();
        if ($row === null) {
            throw new LogicException('Number sequence row could not be locked.');
        }
        $value = (int) $row->next_value;
        $this->db->table('core_number_sequences')->where('id', $row->id)->update(['next_value' => $value + 1, 'updated_at' => $now]);
        $number = preg_replace_callback('/\{(seq):(\d{1,2})\}|\{(jy|jm|jd|gy|gm|gd|branch)\}/', fn (array $m) => $m[1] === 'seq' ? str_pad((string) $value, (int) $m[2], '0', STR_PAD_LEFT) : $tokens[$m[3]], $pattern);

        return $number ?? throw new LogicException('Number formatting failed.');
    }

    public function configure(int $companyId, string $sequence, string $pattern, ?int $actorId = null): void
    {
        $this->guard($companyId, $sequence);
        if (mb_strlen($pattern) > 100 || preg_match('/^(?:\{(?:seq:(?:[1-9]|1[0-2])|jy|jm|jd|gy|gm|gd|branch)\}|[\p{L}\p{N}\/_.\-])+$/uD', $pattern) !== 1 || preg_match_all('/\{seq:/', $pattern) !== 1) {
            throw new InvalidArgumentException('Pattern needs exactly one {seq:1-12} plus supported tokens and safe literals.');
        }
        $this->db->transaction(function () use ($companyId, $sequence, $pattern, $actorId): void {
            $scope = ['company_id' => $companyId, 'sequence' => $sequence];
            $existing = $this->db->table('core_number_formats')->where($scope)->lockForUpdate()->first();
            $now = now();
            if ($existing === null) {
                $id = $this->db->table('core_number_formats')->insertGetId($scope + ['pattern' => $pattern, 'created_at' => $now, 'updated_at' => $now]);
            } else {
                $id = $existing->id;
                $this->db->table('core_number_formats')->where('id', $id)->update(['pattern' => $pattern, 'updated_at' => $now]);
            }
            app(AuditContract::class)->record($companyId, $actorId, 'core', 'core_number_formats', $existing === null ? 'created' : 'updated', $id, $existing === null ? null : ['pattern' => $existing->pattern], ['sequence' => $sequence, 'pattern' => $pattern]);
        });
    }

    private function guard(int $companyId, string $sequence): void
    {
        if ($companyId !== $this->context->companyId()) {
            throw new LogicException('Numbering is restricted to the active company.');
        }
        if (preg_match('/^[a-z][a-z0-9-]*\.[a-z][a-z0-9_.-]{0,100}$/D', $sequence) !== 1) {
            throw new InvalidArgumentException('Sequence must be named <module>.<document>.');
        }
    }
}
