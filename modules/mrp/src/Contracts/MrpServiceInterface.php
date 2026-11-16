<?php

declare(strict_types=1);

namespace Modules\Mrp\Contracts;

interface MrpServiceInterface
{
    public function runStatus(int $companyId, array $params = []): mixed;

    public function plannedOrders(int $companyId, array $params = []): mixed;

    public function pegging(int $companyId, array $params = []): mixed;

    public function firm(int $companyId, array $params = []): mixed;

    public function requirements(int $companyId, array $params = []): mixed;

    public function mrpParams(int $companyId, array $params = []): mixed;
}
