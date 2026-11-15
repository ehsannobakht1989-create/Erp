<?php
declare(strict_types=1);

namespace Modules\TaxMoadian\Contracts;

interface TaxMoadianServiceInterface
{
    public function statusOf(int $companyId, array $params = []): mixed;
    public function submit(int $companyId, array $params = []): mixed;
    public function inquire(int $companyId, array $params = []): mixed;
    public function validate(int $companyId, array $params = []): mixed;
    public function adapter(int $companyId, array $params = []): mixed;
}
