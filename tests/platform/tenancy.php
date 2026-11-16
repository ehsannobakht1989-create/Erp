<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Tenancy\BelongsToCompany;
use App\Support\Tenancy\CompanyContext;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class TenantTestRecord extends Model
{
    use BelongsToCompany;
    use SoftDeletes;

    protected $table = 'tenant_test_records';
    protected $guarded = [];
}
function tenantCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
function tenantThrows(callable $callback, string $message): void
{
    try {
        $callback();
    } catch (LogicException $e) {
        tenantCheck(str_contains($e->getMessage(), $message), $e->getMessage());
        return;
    }
    throw new RuntimeException('Expected failure: '.$message);
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
DB::purge('sqlite');
Schema::create('tenant_test_records', function (Blueprint $table): void {
    $table->id();
    $table->unsignedBigInteger('company_id');
    $table->string('name');
    $table->timestamps();
    $table->softDeletes();
});
$ctx = app(CompanyContext::class);
tenantThrows(fn () => TenantTestRecord::count(), 'context is required');
$ctx->activate(1, 2, 3);
$first = TenantTestRecord::create(['name' => 'first']);
tenantCheck((int) $first->company_id === 1, 'Context must set company_id');
tenantThrows(fn () => TenantTestRecord::create(['name' => 'foreign', 'company_id' => 2]), 'Cross-company');
$ctx->activate(2);
$second = TenantTestRecord::create(['name' => 'second']);
tenantCheck(TenantTestRecord::count() === 1 && TenantTestRecord::first()->id === $second->id, 'Queries must be tenant-scoped');
tenantThrows(function () use ($first): void { $first->name = 'changed'; $first->save(); }, 'Cross-company');
tenantThrows(fn () => $first->delete(), 'Cross-company');
$second->company_id = 1;
tenantThrows(fn () => $second->save(), 'Cross-company');
$ctx->activate(1);
$first->delete();
tenantCheck(TenantTestRecord::count() === 0, 'Soft delete must work within tenant');
$ctx->activate(2);
tenantThrows(fn () => $first->restore(), 'Cross-company');
$ctx->clear();
tenantThrows(fn () => TenantTestRecord::count(), 'context is required');
echo "Tenancy isolation tests passed.\n";
