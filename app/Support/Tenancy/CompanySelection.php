<?php

declare(strict_types=1);

namespace App\Support\Tenancy;

/** Values returned by an authorization-aware resolver, never directly from request input. */
final readonly class CompanySelection
{
    public function __construct(
        public int $companyId,
        public ?int $branchId = null,
        public ?int $fiscalYearId = null,
    ) {}
}
