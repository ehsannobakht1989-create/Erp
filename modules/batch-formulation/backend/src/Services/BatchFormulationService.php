<?php
declare(strict_types=1);

namespace Modules\BatchFormulation\Services;

use Core\Database\Connection;
use Modules\BatchFormulation\Contracts\BatchFormulationServiceInterface;

final class BatchFormulationService implements BatchFormulationServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function activeFormula(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function scale(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function validateBatch(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function yield(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['batch-formulation.batch.started', 'batch-formulation.deviation.raised'];
    }
}
