<?php
declare(strict_types=1);

namespace Modules\SupplierEvaluation\Services;

use Core\Database\Connection;
use Modules\SupplierEvaluation\Contracts\SupplierEvaluationServiceInterface;

final class SupplierEvaluationService implements SupplierEvaluationServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function score(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function grade(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function isApproved(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function asl(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function expiringCerts(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
