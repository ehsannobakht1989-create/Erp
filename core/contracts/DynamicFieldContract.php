<?php
declare(strict_types=1);

namespace Core\Contracts;

interface DynamicFieldContract
{
    /** @return array<int, array<string, mixed>> */
    public function fieldsFor(int $companyId, string $entity): array;
}
