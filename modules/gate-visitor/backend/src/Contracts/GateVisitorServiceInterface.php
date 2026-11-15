<?php
declare(strict_types=1);

namespace Modules\GateVisitor\Contracts;

interface GateVisitorServiceInterface
{
    public function onSite(int $companyId, array $params = []): mixed;
    public function materialPass(int $companyId, array $params = []): mixed;
    public function isBlacklisted(int $companyId, array $params = []): mixed;
}
