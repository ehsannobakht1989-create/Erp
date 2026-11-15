<?php
declare(strict_types=1);

namespace Modules\FixedAssets\Services;

use Core\Database\Connection;
use Modules\FixedAssets\Contracts\FixedAssetsServiceInterface;

final class FixedAssetsService implements FixedAssetsServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function bookValue(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function schedule(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function registerFromInvoiceLine(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function runDepreciation(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
