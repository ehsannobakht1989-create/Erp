<?php
declare(strict_types=1);

namespace Modules\Attendance\Contracts;

interface AttendanceServiceInterface
{
    public function summary(int $companyId, array $params = []): mixed;
    public function balance(int $companyId, array $params = []): mixed;
    public function isOnLeave(int $companyId, array $params = []): mixed;
    public function presentNow(int $companyId, array $params = []): mixed;
    public function workedMinutes(int $companyId, array $params = []): mixed;
}
