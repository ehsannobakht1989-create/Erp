<?php

declare(strict_types=1);

namespace App\Support\Tenancy;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LogicException;

trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $builder): void {
            $builder->where($builder->getModel()->qualifyColumn('company_id'), app(CompanyContext::class)->companyId());
        });
        static::saving(function (Model $model): void {
            $companyId = app(CompanyContext::class)->companyId();
            if (! $model->exists && $model->getAttribute('company_id') === null) {
                $model->setAttribute('company_id', $companyId);
            }
            if ((string) $model->getAttribute('company_id') !== (string) $companyId ||
                ($model->exists && (string) $model->getRawOriginal('company_id') !== (string) $companyId)) {
                throw new LogicException('Cross-company mutation is forbidden.');
            }
        });
        foreach (['deleting', 'restoring'] as $event) {
            static::registerModelEvent($event, function (Model $model): void {
                if ((string) $model->getRawOriginal('company_id') !== (string) app(CompanyContext::class)->companyId()) {
                    throw new LogicException('Cross-company mutation is forbidden.');
                }
            });
        }
    }
}
