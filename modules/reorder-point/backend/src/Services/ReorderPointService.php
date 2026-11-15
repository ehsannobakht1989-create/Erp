<?php
declare(strict_types=1);

namespace Modules\ReorderPoint\Services;

use Core\Database\Connection;
use Modules\ReorderPoint\Contracts\ReorderPointServiceInterface;

final class ReorderPointService implements ReorderPointServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function policy(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function suggestions(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function classOf(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function compute(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['reorder-point.suggestion.created', 'reorder-point.policy.changed', 'reorder-point.class.updated'];
    }
}
