<?php

declare(strict_types=1);

namespace Modules\OrgStructure\Contracts;

interface OrgStructureServiceInterface
{
    public function unitsTree(int $companyId, array $params = []): mixed;

    public function unit(int $companyId, array $params = []): mixed;

    public function managerOf(int $companyId, array $params = []): mixed;

    public function approverChain(int $companyId, array $params = []): mixed;

    public function costCenter(int $companyId, array $params = []): mixed;

    public function costCenters(int $companyId, array $params = []): mixed;

    public function isWorkday(int $companyId, array $params = []): mixed;

    public function workingMinutes(int $companyId, array $params = []): mixed;

    public function shift(int $companyId, array $params = []): mixed;

    public function shiftsFor(int $companyId, array $params = []): mixed;
}
