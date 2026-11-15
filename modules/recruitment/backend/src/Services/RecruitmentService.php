<?php
declare(strict_types=1);

namespace Modules\Recruitment\Services;

use Core\Database\Connection;
use Modules\Recruitment\Contracts\RecruitmentServiceInterface;

final class RecruitmentService implements RecruitmentServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function pipeline(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function sourceStats(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function timeToHire(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['recruitment.application.hired', 'recruitment.requisition.filled'];
    }
}
