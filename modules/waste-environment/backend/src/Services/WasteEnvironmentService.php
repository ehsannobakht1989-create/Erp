<?php
declare(strict_types=1);

namespace Modules\WasteEnvironment\Services;

use Core\Database\Connection;
use Modules\WasteEnvironment\Contracts\WasteEnvironmentServiceInterface;

final class WasteEnvironmentService implements WasteEnvironmentServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function stored(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function contractorsValid(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function exceedances(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
