<?php
declare(strict_types=1);

namespace Modules\TrainingLms\Services;

use Core\Database\Connection;
use Modules\TrainingLms\Contracts\TrainingLmsServiceInterface;

final class TrainingLmsService implements TrainingLmsServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function isCompliant(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function expiring(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function history(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function compliance(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
