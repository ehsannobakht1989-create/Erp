<?php
declare(strict_types=1);

namespace Modules\Rfq\Contracts;

interface RfqServiceInterface
{
    public function create(int $companyId, array $params = []): mixed;
    public function getAward(int $companyId, array $params = []): mixed;
    public function bestPrice(int $companyId, array $params = []): mixed;
    public function history(int $companyId, array $params = []): mixed;
}
