<?php
declare(strict_types=1);

namespace Modules\TrainingLms\Contracts;

interface TrainingLmsServiceInterface
{
    public function isCompliant(int $companyId, array $params = []): mixed;
    public function expiring(int $companyId, array $params = []): mixed;
    public function history(int $companyId, array $params = []): mixed;
    public function compliance(int $companyId, array $params = []): mixed;
}
