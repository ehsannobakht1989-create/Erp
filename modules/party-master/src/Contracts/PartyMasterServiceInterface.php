<?php

declare(strict_types=1);

namespace Modules\PartyMaster\Contracts;

interface PartyMasterServiceInterface
{
    public function find(int $companyId, array $params = []): mixed;

    public function findByNationalId(int $companyId, array $params = []): mixed;

    public function lookup(int $companyId, array $params = []): mixed;

    public function isActive(int $companyId, array $params = []): mixed;

    public function getAddresses(int $companyId, array $params = []): mixed;

    public function getDefaultBank(int $companyId, array $params = []): mixed;

    public function getCreditLimit(int $companyId, array $params = []): mixed;

    public function getPaymentTerm(int $companyId, array $params = []): mixed;

    public function validateIds(int $companyId, array $params = []): mixed;

    public function markUsed(int $companyId, array $params = []): mixed;
}
