<?php
declare(strict_types=1);

namespace Modules\ProjectMgmt\Services;

use Core\Database\Connection;
use Modules\ProjectMgmt\Contracts\ProjectMgmtServiceInterface;

final class ProjectMgmtService implements ProjectMgmtServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function project(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function tasks(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function progress(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function evm(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
