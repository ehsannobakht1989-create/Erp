<?php
declare(strict_types=1);

namespace Modules\Rfq\Services;

use Core\Database\Connection;
use Modules\Rfq\Contracts\RfqServiceInterface;

final class RfqService implements RfqServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function create(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function getAward(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function bestPrice(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function history(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['rfq.rfq.sent'];
    }
}
