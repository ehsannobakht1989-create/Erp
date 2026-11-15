<?php
declare(strict_types=1);

namespace Modules\EcoPlm\Contracts;

interface EcoPlmServiceInterface
{
    public function pendingChanges(int $companyId, array $params = []): mixed;
    public function effectiveRevision(int $companyId, array $params = []): mixed;
    public function impact(int $companyId, array $params = []): mixed;
    public function lifecycle(int $companyId, array $params = []): mixed;
}
