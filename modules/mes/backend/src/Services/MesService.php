<?php
declare(strict_types=1);

namespace Modules\Mes\Services;

use Core\Database\Connection;
use Modules\Mes\Contracts\MesServiceInterface;

final class MesService implements MesServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function runningOps(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function oeeInputs(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function downtime(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function tagValue(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function operatorStatus(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['mes.operation.started', 'mes.production.reported', 'mes.downtime.started', 'mes.andon.raised'];
    }
}
