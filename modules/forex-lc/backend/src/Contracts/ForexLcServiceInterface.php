<?php
declare(strict_types=1);

namespace Modules\ForexLc\Contracts;

interface ForexLcServiceInterface
{
    public function exposure(int $companyId, array $params = []): mixed;
    public function lcFor(int $companyId, array $params = []): mixed;
    public function guaranteesExpiring(int $companyId, array $params = []): mixed;
    public function rateAt(int $companyId, array $params = []): mixed;
}
