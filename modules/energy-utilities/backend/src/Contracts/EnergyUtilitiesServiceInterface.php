<?php
declare(strict_types=1);

namespace Modules\EnergyUtilities\Contracts;

interface EnergyUtilitiesServiceInterface
{
    public function consumption(int $companyId, array $params = []): mixed;
    public function cost(int $companyId, array $params = []): mixed;
    public function sec(int $companyId, array $params = []): mixed;
    public function allocation(int $companyId, array $params = []): mixed;
}
