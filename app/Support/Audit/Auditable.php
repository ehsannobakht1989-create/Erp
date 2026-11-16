<?php

declare(strict_types=1);

namespace App\Support\Audit;

use App\Support\Contracts\AuditContract;
use App\Support\Tenancy\CompanyContext;
use LogicException;

/** Model-instance mutations only. Bulk/raw queries do not fire these hooks. */
trait Auditable
{
    abstract protected function auditModule(): string;

    public static function bootAuditable(): void
    {
        $guard = function (self $model): void {
            if ($model->getConnection()->transactionLevel() < 1) {
                throw new LogicException('Audited mutations require an explicit database transaction.');
            }
            $companyId = app(CompanyContext::class)->companyId();
            if (! $model->exists && $model->getAttribute('company_id') === null) {
                $model->setAttribute('company_id', $companyId);
            }
            if ((int) $model->getAttribute('company_id') !== $companyId) {
                throw new LogicException('Cross-company audit is forbidden.');
            }
            $module = $model->auditModule();
            if (! preg_match('/^[a-z][a-z0-9-]*$/D', $module) || ! str_starts_with($model->getTable(), str_replace('-', '_', $module).'_')) {
                throw new LogicException('Audited table must use its owning module prefix.');
            }
        };
        static::saving($guard);
        static::deleting($guard);
        static::registerModelEvent('restoring', $guard);
        static::created(fn (self $model) => $model->writeAudit('created', null, $model->getAttributes()));
        static::updated(function (self $model): void {
            $after = $model->getChanges();
            $before = array_intersect_key($model->getRawOriginal(), $after);
            $model->writeAudit('updated', $before, $after);
        });
        static::deleted(fn (self $model) => $model->writeAudit('deleted', $model->getRawOriginal(), null));
    }

    /** @param array<string, mixed>|null $before
     * @param  array<string, mixed>|null  $after
     */
    protected function writeAudit(string $action, ?array $before, ?array $after): void
    {
        $actor = app()->bound('request') ? app('request')->user()?->getAuthIdentifier() : null;
        if ($actor !== null && (! ctype_digit((string) $actor) || (int) $actor < 1)) {
            throw new LogicException('Audit actor must be a positive integer.');
        }
        $excluded = array_fill_keys(array_merge($this->getHidden(), ['password', 'password_confirmation', 'remember_token', 'api_token', 'two_factor_secret', 'two_factor_recovery_codes']), true);
        $before = $before === null ? null : array_diff_key($before, $excluded);
        $after = $after === null ? null : array_diff_key($after, $excluded);
        app(AuditContract::class)->record(
            app(CompanyContext::class)->companyId(),
            $actor === null ? null : (int) $actor,
            $this->auditModule(),
            $this->getTable(),
            $action,
            $this->getKey(),
            $before,
            $after,
        );
    }
}
