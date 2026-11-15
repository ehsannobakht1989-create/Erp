<?php
declare(strict_types=1);

namespace Modules\CompetencyAppraisal\Services;

use Core\Database\Connection;
use Modules\CompetencyAppraisal\Contracts\CompetencyAppraisalServiceInterface;

final class CompetencyAppraisalService implements CompetencyAppraisalServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function gaps(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function latestScore(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function competencyMatrix(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
