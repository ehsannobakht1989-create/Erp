<?php
declare(strict_types=1);

namespace Modules\Hse\Contracts;

interface HseServiceInterface
{
    public function openPermits(int $companyId, array $params = []): mixed;
    public function incidentStats(int $companyId, array $params = []): mixed;
    public function ppeValid(int $companyId, array $params = []): mixed;
    public function overdueActions(int $companyId, array $params = []): mixed;
}
