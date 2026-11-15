<?php
declare(strict_types=1);

namespace Modules\ImportCustoms\Contracts;

interface ImportCustomsServiceInterface
{
    public function fileForPo(int $companyId, array $params = []): mixed;
    public function eta(int $companyId, array $params = []): mixed;
    public function landedCost(int $companyId, array $params = []): mixed;
    public function openFiles(int $companyId, array $params = []): mixed;
}
