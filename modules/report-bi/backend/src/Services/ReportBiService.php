<?php
declare(strict_types=1);

namespace Modules\ReportBi\Services;

use Core\Database\Connection;
use Modules\ReportBi\Contracts\ReportBiServiceInterface;

final class ReportBiService implements ReportBiServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function run(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function datasets(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function kpiValue(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function runSchedule(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['report-bi.report.published', 'report-bi.kpi.breached'];
    }
}
