<?php
declare(strict_types=1);

namespace Modules\Lims\Services;

use Core\Database\Connection;
use Modules\Lims\Contracts\LimsServiceInterface;

final class LimsService implements LimsServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function resultsForLot(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function isLabReleased(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function pendingTests(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function tatStats(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['lims.sample.registered', 'lims.result.approved', 'lims.coa.issued'];
    }
}
