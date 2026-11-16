<?php

declare(strict_types=1);

namespace Modules\SupplierEvaluation\Contracts;

interface SupplierEvaluationServiceInterface
{
    public function score(int $companyId, array $params = []): mixed;

    public function grade(int $companyId, array $params = []): mixed;

    public function isApproved(int $companyId, array $params = []): mixed;

    public function asl(int $companyId, array $params = []): mixed;

    public function expiringCerts(int $companyId, array $params = []): mixed;
}
