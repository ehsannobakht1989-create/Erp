<?php
declare(strict_types=1);

namespace Modules\Cmms\Contracts;

interface CmmsServiceInterface
{
    public function openOrders(int $companyId, array $params = []): mixed;
    public function pmDue(int $companyId, array $params = []): mixed;
    public function downtime(int $companyId, array $params = []): mixed;
    public function mttrMtbf(int $companyId, array $params = []): mixed;
    public function costByAsset(int $companyId, array $params = []): mixed;
}
