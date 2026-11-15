<?php
declare(strict_types=1);

namespace Modules\Contracts\Contracts;

interface ContractsServiceInterface
{
    public function activeFor(int $companyId, array $params = []): mixed;
    public function consumed(int $companyId, array $params = []): mixed;
    public function expiring(int $companyId, array $params = []): mixed;
    public function terms(int $companyId, array $params = []): mixed;
}
