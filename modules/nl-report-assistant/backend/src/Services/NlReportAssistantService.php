<?php
declare(strict_types=1);

namespace Modules\NlReportAssistant\Services;

use Core\Database\Connection;
use Modules\NlReportAssistant\Contracts\NlReportAssistantServiceInterface;

final class NlReportAssistantService implements NlReportAssistantServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function ask(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function validateSpec(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function save(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
