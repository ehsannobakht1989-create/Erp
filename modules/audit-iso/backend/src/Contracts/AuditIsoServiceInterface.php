<?php
declare(strict_types=1);

namespace Modules\AuditIso\Contracts;

interface AuditIsoServiceInterface
{
    public function programStatus(int $companyId, array $params = []): mixed;
    public function openFindings(int $companyId, array $params = []): mixed;
    public function upcomingAudits(int $companyId, array $params = []): mixed;
}
