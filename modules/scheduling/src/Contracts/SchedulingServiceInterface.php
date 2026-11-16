<?php

declare(strict_types=1);

namespace Modules\Scheduling\Contracts;

interface SchedulingServiceInterface
{
    public function dispatchList(int $companyId, array $params = []): mixed;

    public function loadByWorkCenter(int $companyId, array $params = []): mixed;

    public function promiseDate(int $companyId, array $params = []): mixed;

    public function reschedule(int $companyId, array $params = []): mixed;

    public function conflicts(int $companyId, array $params = []): mixed;
}
