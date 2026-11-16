<?php

declare(strict_types=1);

namespace Modules\DocOcr\Contracts;

interface DocOcrServiceInterface
{
    public function extract(int $companyId, array $params = []): mixed;

    public function result(int $companyId, array $params = []): mixed;

    public function approve(int $companyId, array $params = []): mixed;

    public function textOf(int $companyId, array $params = []): mixed;
}
