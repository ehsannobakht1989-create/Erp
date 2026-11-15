<?php
declare(strict_types=1);

namespace Modules\ArAp\Services;

use Core\Database\Connection;
use Modules\ArAp\Contracts\ArApServiceInterface;

final class ArApService implements ArApServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function balance(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function openItems(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function overdue(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function creditExposure(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function proposePayments(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['ar-ap.invoice.posted'];
    }
}
