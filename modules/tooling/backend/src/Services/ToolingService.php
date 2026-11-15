<?php
declare(strict_types=1);

namespace Modules\Tooling\Services;

use Core\Database\Connection;
use Modules\Tooling\Contracts\ToolingServiceInterface;

final class ToolingService implements ToolingServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function availability(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function lifeLeft(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function required(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function amortizationPerUnit(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['tooling.tool.checked_out'];
    }
}
