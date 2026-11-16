<?php

declare(strict_types=1);

namespace Modules\CompetencyAppraisal\Contracts;

interface CompetencyAppraisalServiceInterface
{
    public function gaps(int $companyId, array $params = []): mixed;

    public function latestScore(int $companyId, array $params = []): mixed;

    public function competencyMatrix(int $companyId, array $params = []): mixed;
}
