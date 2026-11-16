<?php

declare(strict_types=1);

namespace App\Support\Contracts;

interface NumberingContract
{
    public function next(int $companyId, string $sequence, ?int $branchId = null, ?int $fiscalYearId = null): string;
}
