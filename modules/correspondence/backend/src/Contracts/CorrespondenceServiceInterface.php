<?php
declare(strict_types=1);

namespace Modules\Correspondence\Contracts;

interface CorrespondenceServiceInterface
{
    public function inbox(int $companyId, array $params = []): mixed;
    public function byNumber(int $companyId, array $params = []): mixed;
    public function overdue(int $companyId, array $params = []): mixed;
}
