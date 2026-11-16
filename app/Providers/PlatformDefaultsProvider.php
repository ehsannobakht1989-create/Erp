<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\Contracts;
use App\Support\Defaults;
use App\Support\Numbering\DatabaseNumbering;
use App\Support\Tenancy\CompanyContext;
use Illuminate\Support\ServiceProvider;

/** Register before modules so enabled providers may explicitly replace defaults. */
final class PlatformDefaultsProvider extends ServiceProvider
{
    public function register(): void
    {
        foreach ([Contracts\AuthContract::class, Contracts\CompanyContract::class, Contracts\PermissionContract::class] as $contract) {
            $this->app->bind($contract, Defaults\DenyPlatformAccess::class);
        }
        foreach ([Contracts\AuditContract::class, Contracts\DatasetContract::class, Contracts\NotifierContract::class, Contracts\PrintContract::class, Contracts\WorkflowContract::class] as $contract) {
            $this->app->bind($contract, Defaults\UnavailablePlatform::class);
        }
        $this->app->bind(Contracts\NumberingContract::class, fn ($app) => new DatabaseNumbering($app['db']->connection(), $app->make(CompanyContext::class)));
        $this->app->bind(Contracts\DynamicFieldContract::class, Defaults\EmptyDynamicFields::class);
        $this->app->bind(Contracts\AiGatewayContract::class, Defaults\DisabledAiGateway::class);
    }
}
