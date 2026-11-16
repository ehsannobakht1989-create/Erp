<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Contracts\DataScopeContract;
use App\Support\Contracts\DatasetContract;
use App\Support\Contracts\PermissionContract;
use App\Support\Datasets\DatasetProvider;
use App\Support\Datasets\DatasetRegistry;
use App\Support\Modules\ModuleCatalog;
use App\Support\Tenancy\CompanyContext;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class StockDataset implements DatasetProvider
{
    public function definition(): array
    {
        return [
            'permission' => 'fixture.stock.view',
            'columns' => ['item' => ['type' => 'string'], 'qty' => ['type' => 'decimal'], 'branch' => ['type' => 'int', 'source' => 'branch_id']],
            'scope_columns' => ['branch' => 'branch_id', 'own' => 'created_by'],
        ];
    }

    public function baseQuery(): Builder
    {
        return DB::table('fixture_stock');
    }
}
final class FakeScope implements DataScopeContract
{
    public array $scope = ['level' => 'all', 'values' => []];

    public function scopeFor(int $userId, int $companyId, string $permission): array
    {
        return $this->scope;
    }
}
function dsCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
function dsRejects(callable $call, string $exception, string $message): void
{
    try {
        $call();
    } catch (Throwable $e) {
        dsCheck($e instanceof $exception, $message.' (got '.$e::class.')');

        return;
    }
    throw new RuntimeException($message);
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
DB::purge('sqlite');
Schema::create('fixture_stock', function (Blueprint $table): void {
    $table->id();
    $table->unsignedBigInteger('company_id');
    $table->unsignedBigInteger('branch_id');
    $table->unsignedBigInteger('created_by');
    $table->string('item');
    $table->decimal('qty', 20, 4);
});
DB::table('fixture_stock')->insert([
    ['company_id' => 1, 'branch_id' => 10, 'created_by' => 1, 'item' => 'bolt', 'qty' => '5.0000'],
    ['company_id' => 1, 'branch_id' => 20, 'created_by' => 2, 'item' => 'nut', 'qty' => '7.5000'],
    ['company_id' => 1, 'branch_id' => 10, 'created_by' => 2, 'item' => "o'ring%", 'qty' => '1.0000'],
    ['company_id' => 2, 'branch_id' => 10, 'created_by' => 1, 'item' => 'foreign', 'qty' => '99.0000'],
]);
$path = sys_get_temp_dir().'/erp-datasets-'.bin2hex(random_bytes(6));
mkdir($path.'/fixture', 0777, true);
file_put_contents($path.'/fixture/module.json', json_encode(['id' => 'fixture', 'requires' => [], 'optional' => [], 'datasets' => ['fixture.stock']], JSON_THROW_ON_ERROR));
$context = app(CompanyContext::class);
$registry = new DatasetRegistry(ModuleCatalog::scan($path), $context);
unlink($path.'/fixture/module.json');
rmdir($path.'/fixture');
rmdir($path);
dsCheck(app(DatasetContract::class) instanceof DatasetRegistry, 'Core dataset registry must be bound');
dsRejects(fn () => $registry->register('fixture.undeclared', StockDataset::class), InvalidArgumentException::class, 'Undeclared dataset accepted');
$registry->register('fixture.stock', StockDataset::class);
dsRejects(fn () => $registry->register('fixture.stock', StockDataset::class), InvalidArgumentException::class, 'Duplicate dataset accepted');
$app->instance(PermissionContract::class, new class implements PermissionContract
{
    public function can(int $userId, int $companyId, string $permission): bool
    {
        return in_array($userId, [1, 2], true) && $permission === 'fixture.stock.view';
    }
});
$context->activate(1);
dsRejects(fn () => $registry->query(1, 3, 'fixture.stock', []), AuthorizationException::class, 'Missing permission must deny');
$none = $registry->query(1, 1, 'fixture.stock', []);
dsCheck($none['rows'] === [] && $none['meta']['scope'] === 'none' && $none['meta']['restricted'], 'Default data scope must return no rows');
$scope = new FakeScope;
$app->instance(DataScopeContract::class, $scope);
$all = $registry->query(1, 1, 'fixture.stock', ['sort' => [['item', 'asc']]]);
dsCheck(array_column($all['rows'], 'item') === ['bolt', 'nut', "o'ring%"] && $all['meta']['columns'] === ['item' => 'string', 'qty' => 'decimal', 'branch' => 'int'], 'All scope must stay inside active company');
$scope->scope = ['level' => 'branch', 'values' => [10]];
dsCheck(array_column($registry->query(1, 1, 'fixture.stock', ['columns' => ['item', 'branch']])['rows'], 'branch') === [10, 10], 'Branch scope must filter rows');
$scope->scope = ['level' => 'branch', 'values' => []];
dsCheck($registry->query(1, 1, 'fixture.stock', [])['rows'] === [], 'Empty branch scope must deny rows');
$scope->scope = ['level' => 'own', 'values' => []];
dsCheck(array_column($registry->query(1, 2, 'fixture.stock', ['sort' => [['item', 'asc']]])['rows'], 'item') === ['nut', "o'ring%"], 'Own scope must filter by owner');
$scope->scope = ['level' => 'department', 'values' => [1]];
dsCheck($registry->query(1, 1, 'fixture.stock', [])['meta']['restricted'] === true, 'Unsupported scope column must fail closed');
$scope->scope = ['level' => 'all', 'values' => []];
$filtered = $registry->query(1, 1, 'fixture.stock', ['columns' => ['item'], 'filters' => [['item', 'contains', "o'ring%"], ['qty', '<', '2']]]);
dsCheck($filtered['rows'] === [['item' => "o'ring%"]], 'Filters must bind user values safely');
dsCheck($registry->query(1, 1, 'fixture.stock', ['filters' => [['item', 'contains', '%']]])['rows'][0]['item'] === "o'ring%", 'LIKE wildcards in values must be literal');
dsCheck(count($registry->query(1, 1, 'fixture.stock', ['limit' => 1, 'offset' => 1])['rows']) === 1, 'Paging must apply');
foreach ([
    ['columns' => ['item; DROP TABLE fixture_stock']], ['columns' => ['company_id']], ['filters' => [['item', 'like', 'x']]],
    ['filters' => [['created_by', '=', 1]]], ['filters' => [['qty', 'in', [['nested']]]]], ['sort' => [['qty', 'sideways']]],
    ['limit' => 1001], ['offset' => -1], ['raw' => 'SELECT 1'],
] as $invalid) {
    dsRejects(fn () => $registry->query(1, 1, 'fixture.stock', $invalid), InvalidArgumentException::class, 'Invalid query accepted: '.json_encode($invalid));
}
dsCheck(DB::table('fixture_stock')->count() === 4, 'Dataset queries must not mutate data');
dsRejects(fn () => $registry->query(2, 1, 'fixture.stock', []), LogicException::class, 'Cross-company dataset query accepted');
dsRejects(fn () => $registry->query(1, 1, 'fixture.unknown', []), InvalidArgumentException::class, 'Unknown dataset accepted');
dsCheck($registry->available(1) === ['fixture.stock'] && $registry->available(3) === [], 'Dataset catalog must honour permissions');
echo "Dataset registry permission and row-scope tests passed.\n";
