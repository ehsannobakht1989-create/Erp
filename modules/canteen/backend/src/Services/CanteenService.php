<?php
declare(strict_types=1);

namespace Modules\Canteen\Services;

use Core\Database\Connection;
use Modules\Canteen\Contracts\CanteenServiceInterface;

final class CanteenService implements CanteenServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function entitled(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function chargesFor(int $companyId, array $params = []): mixed
    {
        return [];
    }
}
