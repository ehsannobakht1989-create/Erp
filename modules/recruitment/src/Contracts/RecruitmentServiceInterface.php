<?php

declare(strict_types=1);

namespace Modules\Recruitment\Contracts;

interface RecruitmentServiceInterface
{
    public function pipeline(int $companyId, array $params = []): mixed;

    public function sourceStats(int $companyId, array $params = []): mixed;

    public function timeToHire(int $companyId, array $params = []): mixed;
}
