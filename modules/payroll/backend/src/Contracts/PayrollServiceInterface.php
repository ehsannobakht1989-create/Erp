<?php
declare(strict_types=1);

namespace Modules\Payroll\Contracts;

interface PayrollServiceInterface
{
    public function payslip(int $companyId, array $params = []): mixed;
    public function costByCostCenter(int $companyId, array $params = []): mixed;
    public function headcountCost(int $companyId, array $params = []): mixed;
    public function previewRun(int $companyId, array $params = []): mixed;
}
