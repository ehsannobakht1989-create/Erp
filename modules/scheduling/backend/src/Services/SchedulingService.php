<?php
declare(strict_types=1);

namespace Modules\Scheduling\Services;

use Core\Database\Connection;
use Modules\Scheduling\Contracts\SchedulingServiceInterface;

final class SchedulingService implements SchedulingServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function dispatchList(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function loadByWorkCenter(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function promiseDate(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function reschedule(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function conflicts(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['scheduling.schedule.published', 'scheduling.conflict.detected'];
    }
}
