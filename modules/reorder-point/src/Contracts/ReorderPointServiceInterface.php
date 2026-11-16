<?php

declare(strict_types=1);

namespace Modules\ReorderPoint\Contracts;

interface ReorderPointServiceInterface
{
    public function policy(int $companyId, array $params = []): mixed;

    public function suggestions(int $companyId, array $params = []): mixed;

    public function classOf(int $companyId, array $params = []): mixed;

    public function compute(int $companyId, array $params = []): mixed;
}
