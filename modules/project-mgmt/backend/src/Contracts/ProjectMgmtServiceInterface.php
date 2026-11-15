<?php
declare(strict_types=1);

namespace Modules\ProjectMgmt\Contracts;

interface ProjectMgmtServiceInterface
{
    public function project(int $companyId, array $params = []): mixed;
    public function tasks(int $companyId, array $params = []): mixed;
    public function progress(int $companyId, array $params = []): mixed;
    public function evm(int $companyId, array $params = []): mixed;
}
