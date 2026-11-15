<?php
declare(strict_types=1);

namespace Modules\NcrCapa\Services;

use Core\Database\Connection;
use Modules\NcrCapa\Contracts\NcrCapaServiceInterface;

final class NcrCapaService implements NcrCapaServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function openNcrs(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function defectStats(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function supplierNcrCount(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function capaDue(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['ncr-capa.ncr.opened', 'ncr-capa.capa.opened', 'ncr-capa.scar.sent'];
    }
}
