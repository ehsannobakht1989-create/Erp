<?php
declare(strict_types=1);

namespace Modules\ImportCustoms\Services;

use Core\Database\Connection;
use Modules\ImportCustoms\Contracts\ImportCustomsServiceInterface;

final class ImportCustomsService implements ImportCustomsServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function fileForPo(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function eta(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function landedCost(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function openFiles(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['import-customs.file.cleared', 'import-customs.landed_cost.ready', 'import-customs.demurrage.alert'];
    }
}
