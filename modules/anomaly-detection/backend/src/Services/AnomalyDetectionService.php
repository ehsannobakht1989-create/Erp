<?php
declare(strict_types=1);

namespace Modules\AnomalyDetection\Services;

use Core\Database\Connection;
use Modules\AnomalyDetection\Contracts\AnomalyDetectionServiceInterface;

final class AnomalyDetectionService implements AnomalyDetectionServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function runProfile(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function detections(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function feedback(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function registerProfile(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
