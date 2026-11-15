<?php
declare(strict_types=1);

namespace Modules\BatchFormulation\Contracts;

interface BatchFormulationServiceInterface
{
    public function activeFormula(int $companyId, array $params = []): mixed;
    public function scale(int $companyId, array $params = []): mixed;
    public function validateBatch(int $companyId, array $params = []): mixed;
    public function yield(int $companyId, array $params = []): mixed;
}
