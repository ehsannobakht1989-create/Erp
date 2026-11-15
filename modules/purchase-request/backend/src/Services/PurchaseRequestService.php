<?php
declare(strict_types=1);

namespace Modules\PurchaseRequest\Services;

use Core\Database\Connection;
use Modules\PurchaseRequest\Contracts\PurchaseRequestServiceInterface;

final class PurchaseRequestService implements PurchaseRequestServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function openLines(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function markConverted(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function release(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function find(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
