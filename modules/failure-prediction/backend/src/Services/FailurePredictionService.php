<?php
declare(strict_types=1);

namespace Modules\FailurePrediction\Services;

use Core\Database\Connection;
use Modules\FailurePrediction\Contracts\FailurePredictionServiceInterface;

final class FailurePredictionService implements FailurePredictionServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function scores(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function explain(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function topRisks(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function feedback(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
