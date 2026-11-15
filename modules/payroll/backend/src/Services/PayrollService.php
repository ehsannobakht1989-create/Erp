<?php
declare(strict_types=1);

namespace Modules\Payroll\Services;

use Core\Database\Connection;
use Modules\Payroll\Contracts\PayrollServiceInterface;

final class PayrollService implements PayrollServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function payslip(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function costByCostCenter(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function headcountCost(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function previewRun(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['payroll.run.approved', 'payroll.run.paid'];
    }
}
