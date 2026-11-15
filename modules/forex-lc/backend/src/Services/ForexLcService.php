<?php
declare(strict_types=1);

namespace Modules\ForexLc\Services;

use Core\Database\Connection;
use Modules\ForexLc\Contracts\ForexLcServiceInterface;

final class ForexLcService implements ForexLcServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function exposure(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function lcFor(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function guaranteesExpiring(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function rateAt(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['forex-lc.lc.status_changed', 'forex-lc.guarantee.expiring', 'forex-lc.fx.settled'];
    }
}
