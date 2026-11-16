<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Contracts\AuditContract;
use App\Support\Contracts\NumberingContract;
use App\Support\Modules\ModuleCatalog;
use App\Support\Numbering\DatabaseNumbering;
use App\Support\Settings\SettingsService;
use App\Support\Tenancy\CompanyContext;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

function snCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
function snRejects(callable $call, string $exception, string $message): void
{
    try {
        $call();
    } catch (Throwable $e) {
        snCheck($e instanceof $exception, $message.' (got '.$e::class.': '.$e->getMessage().')');

        return;
    }
    throw new RuntimeException($message);
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
DB::purge('sqlite');
Artisan::call('migrate', ['--force' => true]);
$audit = new class implements AuditContract
{
    public array $records = [];

    public bool $fail = false;

    public function record(int $companyId, ?int $actorId, string $module, string $table, string $action, int|string $recordId, ?array $before = null, ?array $after = null): void
    {
        if ($this->fail) {
            throw new RuntimeException('audit store down');
        }
        $this->records[] = compact('companyId', 'actorId', 'module', 'table', 'action', 'recordId', 'before', 'after');
    }
};
$app->instance(AuditContract::class, $audit);
$path = sys_get_temp_dir().'/erp-settings-'.bin2hex(random_bytes(6));
mkdir($path.'/fixture', 0777, true);
mkdir($path.'/other', 0777, true);
file_put_contents($path.'/fixture/module.json', json_encode(['id' => 'fixture', 'requires' => [], 'optional' => [], 'settings' => [
    ['key' => 'approval_limit', 'type' => 'decimal', 'default' => '1000.0000'],
    ['key' => 'auto_post', 'type' => 'bool', 'default' => false],
    ['key' => 'labels', 'type' => 'json', 'default' => []],
    ['key' => 'tolerance', 'type' => 'float', 'default' => 0.5],
    ['key' => 'broken', 'type' => 'int', 'default' => 'not-int'],
]], JSON_THROW_ON_ERROR));
file_put_contents($path.'/other/module.json', json_encode(['id' => 'other', 'requires' => [], 'optional' => [], 'settings' => [['key' => 'x', 'type' => 'int', 'default' => 1]]], JSON_THROW_ON_ERROR));
$context = app(CompanyContext::class);
$settings = new SettingsService(ModuleCatalog::scan($path), ['fixture'], DB::connection(), $context);
array_map('unlink', [$path.'/fixture/module.json', $path.'/other/module.json']);
rmdir($path.'/fixture');
rmdir($path.'/other');
rmdir($path);
snRejects(fn () => $settings->get('fixture', 'auto_post'), LogicException::class, 'Settings require company context');
$context->activate(1);
snCheck($settings->get('fixture', 'approval_limit') === '1000.0000' && $settings->get('fixture', 'auto_post') === false, 'Manifest defaults must be typed');
$settings->set('fixture', 'auto_post', true, 7);
$settings->set('fixture', 'approval_limit', '2500.5000', 7);
$settings->set('fixture', 'approval_limit', '3000', 8);
$settings->set('fixture', 'tolerance', 2, 7);
snCheck($settings->get('fixture', 'auto_post') === true && $settings->get('fixture', 'approval_limit') === '3000' && $settings->get('fixture', 'tolerance') === 2.0, 'Stored values must round-trip typed');
snCheck(count($audit->records) === 4 && $audit->records[2]['before']['value'] === '2500.5000' && $audit->records[2]['after']['value'] === '3000' && $audit->records[2]['actorId'] === 8, 'Setting changes must be audited with before/after');
foreach ([['auto_post', 'yes'], ['approval_limit', 1.5], ['approval_limit', '1.23456'], ['labels', 'text'], ['tolerance', NAN], ['missing', 1]] as [$key, $value]) {
    snRejects(fn () => $settings->set('fixture', $key, $value), InvalidArgumentException::class, 'Invalid setting accepted: '.$key);
}
snRejects(fn () => $settings->get('other', 'x'), InvalidArgumentException::class, 'Disabled module settings must be unavailable');
snRejects(fn () => $settings->get('fixture', 'broken'), InvalidArgumentException::class, 'Invalid manifest default must fail loudly');
$audit->fail = true;
snRejects(fn () => $settings->set('fixture', 'labels', ['a']), RuntimeException::class, 'Audit failure must propagate');
$audit->fail = false;
snCheck($settings->get('fixture', 'labels') === [], 'Audit failure must roll back setting change');
$context->activate(2);
snCheck($settings->get('fixture', 'auto_post') === false, 'Settings must be isolated per company');
snRejects(fn () => $settings->all('fixture'), InvalidArgumentException::class, 'all() must not hide invalid manifest defaults');
// Numbering.
$numbering = app(NumberingContract::class);
if (! $numbering instanceof DatabaseNumbering) {
    throw new RuntimeException('Core numbering must be bound by default');
}
$context->activate(1);
snRejects(fn () => $numbering->next(1, 'fixture.receipt'), LogicException::class, 'Numbering outside transaction must fail');
snRejects(fn () => DB::transaction(fn () => $numbering->next(2, 'fixture.receipt')), LogicException::class, 'Cross-company numbering must fail');
snRejects(fn () => DB::transaction(fn () => $numbering->next(1, 'Bad Sequence')), InvalidArgumentException::class, 'Invalid sequence name accepted');
$at = CarbonImmutable::create(2026, 11, 16, 12, 0, 0, 'UTC');
$first = DB::transaction(fn () => [$numbering->next(1, 'fixture.receipt', null, null, $at), $numbering->next(1, 'fixture.receipt', null, null, $at)]);
snCheck($first === ['1405-000001', '1405-000002'], 'Default Jalali-year pattern must be sequential: '.implode(',', $first));
try {
    DB::transaction(function () use ($numbering, $at): void {
        snCheck($numbering->next(1, 'fixture.receipt', null, null, $at) === '1405-000003', 'Expected third number');
        throw new DomainException('business failure');
    });
} catch (Throwable $e) {
    snCheck($e instanceof DomainException, 'Unexpected rollback failure: '.$e->getMessage());
}
snCheck(DB::transaction(fn () => $numbering->next(1, 'fixture.receipt', null, null, $at)) === '1405-000003', 'Rolled-back number must be reused (gap-free)');
snCheck(DB::transaction(fn () => $numbering->next(1, 'fixture.receipt', 5, null, $at)) === '1405-000001', 'Branch counters must be independent');
snCheck(DB::transaction(fn () => $numbering->next(1, 'fixture.receipt', null, null, CarbonImmutable::create(2026, 3, 20, 12, 0, 0, 'UTC'))) === '1404-000001', 'Jalali year must reset sequence');
$numbering->configure(1, 'fixture.invoice', 'INV/{jy}{jm}/{branch}/{seq:4}', 7);
snCheck(DB::transaction(fn () => $numbering->next(1, 'fixture.invoice', 3, 9, $at)) === 'INV/140508/3/0001', 'Configured pattern must format tokens');
snCheck(end($audit->records)['table'] === 'core_number_formats', 'Pattern changes must be audited');
foreach (['{jy}-', '{seq:4}-{seq:2}', '{seq:0}', '{seq:13}', '{foo}{seq:3}', 'INV {seq:3}', '<script>{seq:3}'] as $invalid) {
    snRejects(fn () => $numbering->configure(1, 'fixture.invoice', $invalid), InvalidArgumentException::class, 'Invalid pattern accepted: '.$invalid);
}
$context->activate(2);
snCheck(DB::transaction(fn () => $numbering->next(2, 'fixture.receipt', null, null, $at)) === '1405-000001', 'Company counters must be isolated');
echo "Settings and numbering service tests passed.\n";
