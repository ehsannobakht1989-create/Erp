<?php
declare(strict_types=1);

namespace Modules\Attendance\Services;

use Core\Database\Connection;
use Modules\Attendance\Contracts\AttendanceServiceInterface;

final class AttendanceService implements AttendanceServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function summary(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function balance(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function isOnLeave(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function presentNow(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function workedMinutes(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
