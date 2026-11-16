<?php

declare(strict_types=1);

namespace Modules\Canteen\Contracts;

interface CanteenServiceInterface
{
    public function entitled(int $companyId, array $params = []): mixed;

    public function chargesFor(int $companyId, array $params = []): mixed;
}
