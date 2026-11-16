<?php

declare(strict_types=1);

namespace Modules\QualityInspection\Contracts;

interface QualityInspectionServiceInterface
{
    public function lotStatus(int $companyId, array $params = []): mixed;

    public function isReleased(int $companyId, array $params = []): mixed;

    public function supplierScore(int $companyId, array $params = []): mixed;

    public function openLots(int $companyId, array $params = []): mixed;

    public function spc(int $companyId, array $params = []): mixed;
}
