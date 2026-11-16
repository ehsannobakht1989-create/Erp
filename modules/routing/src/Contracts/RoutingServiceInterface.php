<?php

declare(strict_types=1);

namespace Modules\Routing\Contracts;

interface RoutingServiceInterface
{
    public function findActive(int $companyId, array $params = []): mixed;

    public function workCenter(int $companyId, array $params = []): mixed;

    public function capacity(int $companyId, array $params = []): mixed;

    public function standardTime(int $companyId, array $params = []): mixed;

    public function costRates(int $companyId, array $params = []): mixed;
}
