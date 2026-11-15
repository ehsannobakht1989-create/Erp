<?php
declare(strict_types=1);

namespace Modules\NlReportAssistant\Contracts;

interface NlReportAssistantServiceInterface
{
    public function ask(int $companyId, array $params = []): mixed;
    public function validateSpec(int $companyId, array $params = []): mixed;
    public function save(int $companyId, array $params = []): mixed;
}
