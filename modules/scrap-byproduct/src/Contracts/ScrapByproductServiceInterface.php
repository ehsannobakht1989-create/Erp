<?php

declare(strict_types=1);

namespace Modules\ScrapByproduct\Contracts;

interface ScrapByproductServiceInterface
{
    public function rateByWorkCenter(int $companyId, array $params = []): mixed;

    public function rateByItem(int $companyId, array $params = []): mixed;

    public function expectedByProduct(int $companyId, array $params = []): mixed;

    public function disposition(int $companyId, array $params = []): mixed;
}
