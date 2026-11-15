<?php
declare(strict_types=1);

namespace Modules\Oee\Contracts;

interface OeeServiceInterface
{
    public function oee(int $companyId, array $params = []): mixed;
    public function losses(int $companyId, array $params = []): mixed;
    public function trend(int $companyId, array $params = []): mixed;
    public function benchmark(int $companyId, array $params = []): mixed;
}
