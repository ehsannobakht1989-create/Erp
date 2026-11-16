<?php

declare(strict_types=1);

namespace App\Support\Defaults;

use App\Support\Contracts\DynamicFieldContract;

final class EmptyDynamicFields implements DynamicFieldContract
{
    public function fieldsFor(int $companyId, string $entity): array
    {
        return [];
    }
}
