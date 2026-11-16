<?php

declare(strict_types=1);

namespace Modules\FailurePrediction\Contracts;

interface FailurePredictionServiceInterface
{
    public function scores(int $companyId, array $params = []): mixed;

    public function explain(int $companyId, array $params = []): mixed;

    public function topRisks(int $companyId, array $params = []): mixed;

    public function feedback(int $companyId, array $params = []): mixed;
}
