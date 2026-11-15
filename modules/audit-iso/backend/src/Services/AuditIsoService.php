<?php
declare(strict_types=1);

namespace Modules\AuditIso\Services;

use Core\Database\Connection;
use Modules\AuditIso\Contracts\AuditIsoServiceInterface;

final class AuditIsoService implements AuditIsoServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function programStatus(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function openFindings(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function upcomingAudits(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['audit-iso.audit.completed', 'audit-iso.finding.raised'];
    }
}
