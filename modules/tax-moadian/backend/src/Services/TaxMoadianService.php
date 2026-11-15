<?php
declare(strict_types=1);

namespace Modules\TaxMoadian\Services;

use Core\Database\Connection;
use Modules\TaxMoadian\Contracts\TaxMoadianServiceInterface;

final class TaxMoadianService implements TaxMoadianServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function statusOf(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function submit(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function inquire(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function validate(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function adapter(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['tax-moadian.invoice.queued'];
    }
}
