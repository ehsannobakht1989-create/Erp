<?php

declare(strict_types=1);

namespace App\Support\Contracts;

interface DynamicFieldContract
{
    /** @return array<int, array<string, mixed>> */
    public function fieldsFor(int $companyId, string $entity): array;
}
