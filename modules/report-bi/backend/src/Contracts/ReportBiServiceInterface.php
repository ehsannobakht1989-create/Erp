<?php
declare(strict_types=1);

namespace Modules\ReportBi\Contracts;

interface ReportBiServiceInterface
{
    public function run(int $companyId, array $params = []): mixed;
    public function datasets(int $companyId, array $params = []): mixed;
    public function kpiValue(int $companyId, array $params = []): mixed;
    public function runSchedule(int $companyId, array $params = []): mixed;
}
