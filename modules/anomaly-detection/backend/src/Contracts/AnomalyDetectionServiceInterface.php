<?php
declare(strict_types=1);

namespace Modules\AnomalyDetection\Contracts;

interface AnomalyDetectionServiceInterface
{
    public function runProfile(int $companyId, array $params = []): mixed;
    public function detections(int $companyId, array $params = []): mixed;
    public function feedback(int $companyId, array $params = []): mixed;
    public function registerProfile(int $companyId, array $params = []): mixed;
}
