<?php
declare(strict_types=1);

namespace Core\Stubs;

use Core\Contracts\DynamicFieldContract;

final class NullDynamicField implements DynamicFieldContract
{
    public function fieldsFor(int $companyId, string $entity): array
    {
        return [];
    }
}
