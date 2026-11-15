<?php
declare(strict_types=1);

namespace Modules\QualityInspection\Services;

use Core\Database\Connection;
use Modules\QualityInspection\Contracts\QualityInspectionServiceInterface;

final class QualityInspectionService implements QualityInspectionServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function lotStatus(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function isReleased(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function supplierScore(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function openLots(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function spc(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
