<?php

declare(strict_types=1);

namespace Modules\AccountingGl\Contracts;

interface AccountingGlServiceInterface
{
    public function requestJournal(int $companyId, array $params = []): mixed;

    public function post(int $companyId, array $params = []): mixed;

    public function accountBalance(int $companyId, array $params = []): mixed;

    public function trialBalance(int $companyId, array $params = []): mixed;

    public function isPeriodOpen(int $companyId, array $params = []): mixed;

    public function resolveAccount(int $companyId, array $params = []): mixed;
}
