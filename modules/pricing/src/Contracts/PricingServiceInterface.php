<?php

declare(strict_types=1);

namespace Modules\Pricing\Contracts;

interface PricingServiceInterface
{
    public function resolve(int $companyId, array $params = []): mixed;

    public function resolveBulk(int $companyId, array $params = []): mixed;

    public function explain(int $companyId, array $params = []): mixed;

    public function activeLists(int $companyId, array $params = []): mixed;
}
