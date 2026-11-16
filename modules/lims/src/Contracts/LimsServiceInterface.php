<?php

declare(strict_types=1);

namespace Modules\Lims\Contracts;

interface LimsServiceInterface
{
    public function resultsForLot(int $companyId, array $params = []): mixed;

    public function isLabReleased(int $companyId, array $params = []): mixed;

    public function pendingTests(int $companyId, array $params = []): mixed;

    public function tatStats(int $companyId, array $params = []): mixed;
}
