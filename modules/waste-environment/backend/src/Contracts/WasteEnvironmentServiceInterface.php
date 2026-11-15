<?php
declare(strict_types=1);

namespace Modules\WasteEnvironment\Contracts;

interface WasteEnvironmentServiceInterface
{
    public function stored(int $companyId, array $params = []): mixed;
    public function contractorsValid(int $companyId, array $params = []): mixed;
    public function exceedances(int $companyId, array $params = []): mixed;
}
