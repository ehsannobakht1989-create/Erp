<?php
declare(strict_types=1);

namespace Modules\FixedAssets\Contracts;

interface FixedAssetsServiceInterface
{
    public function bookValue(int $companyId, array $params = []): mixed;
    public function schedule(int $companyId, array $params = []): mixed;
    public function registerFromInvoiceLine(int $companyId, array $params = []): mixed;
    public function runDepreciation(int $companyId, array $params = []): mixed;
}
