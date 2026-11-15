<?php
declare(strict_types=1);

namespace Modules\Bom\Contracts;

interface BomServiceInterface
{
    public function findActive(int $companyId, array $params = []): mixed;
    public function explode(int $companyId, array $params = []): mixed;
    public function whereUsed(int $companyId, array $params = []): mixed;
    public function compare(int $companyId, array $params = []): mixed;
    public function costRollupInputs(int $companyId, array $params = []): mixed;
    public function validate(int $companyId, array $params = []): mixed;
}
