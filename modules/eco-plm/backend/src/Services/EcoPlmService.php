<?php
declare(strict_types=1);

namespace Modules\EcoPlm\Services;

use Core\Database\Connection;
use Modules\EcoPlm\Contracts\EcoPlmServiceInterface;

final class EcoPlmService implements EcoPlmServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function pendingChanges(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function effectiveRevision(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function impact(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function lifecycle(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['eco-plm.change.requested'];
    }
}
