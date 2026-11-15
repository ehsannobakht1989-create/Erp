<?php
declare(strict_types=1);

namespace Modules\Correspondence\Services;

use Core\Database\Connection;
use Modules\Correspondence\Contracts\CorrespondenceServiceInterface;

final class CorrespondenceService implements CorrespondenceServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function inbox(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function byNumber(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function overdue(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['correspondence.letter.registered'];
    }
}
