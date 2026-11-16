<?php

declare(strict_types=1);

namespace App\Support\Tenancy;

use LogicException;

/** Context must only be set after the caller has authorized the user for it. */
final class CompanyContext
{
    private ?int $companyId = null;

    private ?int $branchId = null;

    private ?int $fiscalYearId = null;

    public function activate(int $companyId, ?int $branchId = null, ?int $fiscalYearId = null): void
    {
        if ($companyId <= 0 || ($branchId !== null && $branchId <= 0) || ($fiscalYearId !== null && $fiscalYearId <= 0)) {
            throw new LogicException('Context identifiers must be positive.');
        }
        $this->companyId = $companyId;
        $this->branchId = $branchId;
        $this->fiscalYearId = $fiscalYearId;
    }

    public function companyId(): int
    {
        return $this->companyId ?? throw new LogicException('Company context is required.');
    }

    public function branchId(): ?int
    {
        return $this->branchId;
    }

    public function fiscalYearId(): ?int
    {
        return $this->fiscalYearId;
    }

    public function clear(): void
    {
        $this->companyId = $this->branchId = $this->fiscalYearId = null;
    }

    /** @return array{0:int,1:?int,2:?int}|null */
    public function snapshot(): ?array
    {
        return $this->companyId === null ? null : [$this->companyId, $this->branchId, $this->fiscalYearId];
    }

    /** @param array{0:int,1:?int,2:?int}|null $snapshot */
    public function restore(?array $snapshot): void
    {
        $snapshot === null ? $this->clear() : $this->activate(...$snapshot);
    }
}
