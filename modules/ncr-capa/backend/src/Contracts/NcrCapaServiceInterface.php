<?php
declare(strict_types=1);

namespace Modules\NcrCapa\Contracts;

interface NcrCapaServiceInterface
{
    public function openNcrs(int $companyId, array $params = []): mixed;
    public function defectStats(int $companyId, array $params = []): mixed;
    public function supplierNcrCount(int $companyId, array $params = []): mixed;
    public function capaDue(int $companyId, array $params = []): mixed;
}
