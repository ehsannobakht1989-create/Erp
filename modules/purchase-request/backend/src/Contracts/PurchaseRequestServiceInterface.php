<?php
declare(strict_types=1);

namespace Modules\PurchaseRequest\Contracts;

interface PurchaseRequestServiceInterface
{
    public function openLines(int $companyId, array $params = []): mixed;
    public function markConverted(int $companyId, array $params = []): mixed;
    public function release(int $companyId, array $params = []): mixed;
    public function find(int $companyId, array $params = []): mixed;
}
