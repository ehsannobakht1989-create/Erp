<?php
declare(strict_types=1);

namespace Modules\ScrapByproduct\Services;

use Core\Database\Connection;
use Modules\ScrapByproduct\Contracts\ScrapByproductServiceInterface;

final class ScrapByproductService implements ScrapByproductServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function rateByWorkCenter(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function rateByItem(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function expectedByProduct(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function disposition(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
