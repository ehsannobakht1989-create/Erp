<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Audit\Auditable;
use App\Support\Contracts\AuditContract;
use App\Support\Tenancy\BelongsToCompany;
use App\Support\Tenancy\CompanyContext;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class AuditedFixture extends Model
{
    use Auditable, BelongsToCompany;

    protected $table = 'fixture_records';

    protected $guarded = [];

    protected $hidden = ['secret'];

    protected function auditModule(): string
    {
        return 'fixture';
    }
}
function auditCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
DB::purge('sqlite');
Schema::create('fixture_records', function (Blueprint $table): void {
    $table->id();
    $table->unsignedBigInteger('company_id');
    $table->string('name');
    $table->string('secret')->nullable();
    $table->timestamps();
});
app(CompanyContext::class)->activate(1);
try {
    AuditedFixture::query()->create(['name' => 'unprotected']);
    throw new RuntimeException('Audit accepted nontransactional mutation');
} catch (Throwable $e) {
    auditCheck(str_contains($e->getMessage(), 'transaction'), 'Wrong audit failure');
}
auditCheck(AuditedFixture::query()->count() === 0, 'Nontransactional mutation must be rejected before insert');
try {
    DB::transaction(fn () => AuditedFixture::query()->create(['name' => 'missing audit provider']));
    throw new RuntimeException('Missing audit provider was ignored');
} catch (Throwable $e) {
    auditCheck(str_contains($e->getMessage(), 'unavailable'), 'Wrong missing-provider failure');
}
auditCheck(AuditedFixture::query()->count() === 0, 'Audit failure must roll back business mutation');
$recorder = new class implements AuditContract
{
    public array $records = [];

    public function record(int $companyId, ?int $actorId, string $module, string $table, string $action, int|string $recordId, ?array $before = null, ?array $after = null): void
    {
        $this->records[] = compact('companyId', 'actorId', 'module', 'table', 'action', 'recordId', 'before', 'after');
    }
};
$app->instance(AuditContract::class, $recorder);
$record = DB::transaction(fn () => AuditedFixture::query()->create(['name' => 'original', 'secret' => 'do-not-log']));
DB::transaction(fn () => $record->update(['name' => 'changed']));
DB::transaction(fn () => $record->delete());
auditCheck(count($recorder->records) === 3, 'Create/update/delete must be audited automatically');
[$created, $updated, $deleted] = $recorder->records;
auditCheck($created['companyId'] === 1 && $created['module'] === 'fixture' && $created['table'] === 'fixture_records', 'Audit must include tenant/owner/table');
auditCheck(! str_contains(json_encode($recorder->records, JSON_THROW_ON_ERROR), 'do-not-log'), 'Hidden model fields must not leak into audit');
auditCheck($created['before'] === null && $created['after']['name'] === 'original', 'Create snapshot is incorrect');
auditCheck($updated['before']['name'] === 'original' && $updated['after']['name'] === 'changed', 'Update diff must preserve original and changed values');
auditCheck($deleted['after'] === null && $deleted['action'] === 'deleted', 'Delete snapshot is incorrect');
echo "Automatic audit hook tests passed.\n";
