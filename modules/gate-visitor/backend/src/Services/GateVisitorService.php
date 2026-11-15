<?php
declare(strict_types=1);

namespace Modules\GateVisitor\Services;

use Core\Database\Connection;
use Modules\GateVisitor\Contracts\GateVisitorServiceInterface;

final class GateVisitorService implements GateVisitorServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function onSite(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function materialPass(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function isBlacklisted(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
