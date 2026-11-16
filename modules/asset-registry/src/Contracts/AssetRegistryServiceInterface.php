<?php

declare(strict_types=1);

namespace Modules\AssetRegistry\Contracts;

interface AssetRegistryServiceInterface
{
    public function find(int $companyId, array $params = []): mixed;

    public function tree(int $companyId, array $params = []): mixed;

    public function byQr(int $companyId, array $params = []): mixed;

    public function currentMeter(int $companyId, array $params = []): mixed;

    public function addReading(int $companyId, array $params = []): mixed;

    public function spareParts(int $companyId, array $params = []): mixed;

    public function setStatus(int $companyId, array $params = []): mixed;

    public function lookup(int $companyId, array $params = []): mixed;
}
