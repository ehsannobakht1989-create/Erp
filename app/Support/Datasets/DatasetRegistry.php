<?php

declare(strict_types=1);

namespace App\Support\Datasets;

use App\Support\Contracts\DataScopeContract;
use App\Support\Contracts\DatasetContract;
use App\Support\Contracts\PermissionContract;
use App\Support\Modules\ModuleCatalog;
use App\Support\Tenancy\CompanyContext;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;
use LogicException;

final class DatasetRegistry implements DatasetContract
{
    private const IDENTIFIER = '/^[a-z][a-z0-9_]*(?:\.[a-z][a-z0-9_]*)?$/D';

    private const OPERATORS = ['=', '!=', '<', '<=', '>', '>=', 'in', 'between', 'contains'];

    /** @var array<string, class-string<DatasetProvider>> */
    private array $providers = [];

    public function __construct(private readonly ModuleCatalog $catalog, private readonly CompanyContext $context) {}

    /** @param class-string<DatasetProvider> $provider */
    public function register(string $dataset, string $provider): void
    {
        $module = explode('.', $dataset, 2)[0];
        if (! in_array($dataset, $this->catalog->manifests()[$module]['datasets'] ?? [], true) || isset($this->providers[$dataset])) {
            throw new InvalidArgumentException('Dataset must be declared once in its module manifest: '.$dataset);
        }
        if (! is_subclass_of($provider, DatasetProvider::class)) {
            throw new InvalidArgumentException('Dataset provider must implement DatasetProvider.');
        }
        $this->providers[$dataset] = $provider;
    }

    /** @return list<string> datasets this user may query in the active company */
    public function available(int $userId): array
    {
        $companyId = $this->context->companyId();

        return array_values(array_filter(array_keys($this->providers), fn (string $dataset) => app(PermissionContract::class)->can($userId, $companyId, $this->definition($dataset)['permission'])));
    }

    public function query(int $companyId, int $userId, string $dataset, array $query): array
    {
        if ($companyId !== $this->context->companyId()) {
            throw new LogicException('Datasets are restricted to the active company.');
        }
        $definition = $this->definition($dataset);
        if (! app(PermissionContract::class)->can($userId, $companyId, $definition['permission'])) {
            throw new AuthorizationException('Dataset permission denied.');
        }
        if (array_diff(array_keys($query), ['columns', 'filters', 'sort', 'limit', 'offset']) !== []) {
            throw new InvalidArgumentException('Unsupported dataset query key.');
        }
        $columns = $definition['columns'];
        $selected = $query['columns'] ?? array_keys($columns);
        if (! is_array($selected) || $selected === [] || array_diff($selected, array_keys($columns)) !== []) {
            throw new InvalidArgumentException('Unknown dataset column requested.');
        }
        $limit = $query['limit'] ?? 100;
        $offset = $query['offset'] ?? 0;
        if (! is_int($limit) || $limit < 1 || $limit > 1000 || ! is_int($offset) || $offset < 0) {
            throw new InvalidArgumentException('Limit must be 1-1000 and offset non-negative.');
        }
        $builder = $this->provider($dataset)->baseQuery()->where($definition['company_column'] ?? 'company_id', $companyId);
        $scope = app(DataScopeContract::class)->scopeFor($userId, $companyId, $definition['permission']);
        $restricted = ! $this->applyScope($builder, $scope, $definition['scope_columns'] ?? [], $userId);
        foreach ($query['filters'] ?? [] as $filter) {
            $this->applyFilter($builder, $filter, $columns);
        }
        foreach ($query['sort'] ?? [] as $sort) {
            if (! is_array($sort) || count($sort) !== 2 || ! isset($columns[$sort[0]]) || ! in_array($sort[1], ['asc', 'desc'], true)) {
                throw new InvalidArgumentException('Sort must be [column, asc|desc].');
            }
            $builder->orderBy($this->source($sort[0], $columns), $sort[1]);
        }
        foreach ($selected as $name) {
            $builder->addSelect($this->source($name, $columns).' as '.$name);
        }
        $rows = $restricted ? [] : array_map(fn ($row) => (array) $row, $builder->limit($limit)->offset($offset)->get()->all());

        return ['rows' => $rows, 'meta' => ['dataset' => $dataset, 'columns' => array_intersect_key(array_map(fn ($c) => $c['type'], $columns), array_flip($selected)), 'count' => count($rows), 'limit' => $limit, 'offset' => $offset, 'scope' => $scope['level'], 'restricted' => $restricted]];
    }

    /** @return array{permission: string, company_column?: string, columns: array<string, array{type: string, source?: string}>, scope_columns?: array<string, string>} */
    private function definition(string $dataset): array
    {
        $definition = $this->provider($dataset)->definition();
        foreach ($definition['columns'] as $name => $column) {
            if (preg_match('/^[a-z][a-z0-9_]*$/D', $name) !== 1 || preg_match(self::IDENTIFIER, $column['source'] ?? $name) !== 1 || ! in_array($column['type'], ['string', 'int', 'decimal', 'date', 'datetime', 'bool'], true)) {
                throw new LogicException('Invalid dataset column definition: '.$dataset.'.'.$name);
            }
        }
        foreach (array_merge([$definition['company_column'] ?? 'company_id'], array_values($definition['scope_columns'] ?? [])) as $identifier) {
            if (preg_match(self::IDENTIFIER, $identifier) !== 1) {
                throw new LogicException('Invalid dataset scope column: '.$dataset);
            }
        }

        return $definition;
    }

    private function provider(string $dataset): DatasetProvider
    {
        $class = $this->providers[$dataset] ?? throw new InvalidArgumentException('Unknown or disabled dataset: '.$dataset);

        return app($class);
    }

    /**
     * @param  array{level: string, values: list<int>}  $scope
     * @param  array<string, string>  $scopeColumns
     */
    private function applyScope(Builder $builder, array $scope, array $scopeColumns, int $userId): bool
    {
        if ($scope['level'] === 'all') {
            return true;
        }
        $column = $scopeColumns[$scope['level']] ?? null;
        if ($column === null) {
            return false;
        }
        if ($scope['level'] === 'own') {
            $builder->where($column, $userId);
        } elseif ($scope['values'] === []) {
            return false;
        } else {
            $builder->whereIn($column, $scope['values']);
        }

        return true;
    }

    /** @param array<string, array{type: string, source?: string}> $columns */
    private function applyFilter(Builder $builder, mixed $filter, array $columns): void
    {
        if (! is_array($filter) || count($filter) !== 3 || ! is_string($filter[0]) || ! isset($columns[$filter[0]]) || ! in_array($filter[1], self::OPERATORS, true)) {
            throw new InvalidArgumentException('Filter must be [known column, supported operator, value].');
        }
        [$name, $operator, $value] = $filter;
        $source = $this->source($name, $columns);
        $scalar = fn (mixed $v): bool => is_string($v) || is_int($v) || is_bool($v);
        match ($operator) {
            'in' => is_array($value) && $value !== [] && count($value) <= 500 && array_filter($value, $scalar) === $value ? $builder->whereIn($source, array_values($value)) : throw new InvalidArgumentException('in requires 1-500 scalar values.'),
            'between' => is_array($value) && count($value) === 2 && $scalar($value[0] ?? null) && $scalar($value[1] ?? null) ? $builder->whereBetween($source, [$value[0], $value[1]]) : throw new InvalidArgumentException('between requires two scalar values.'),
            // arch-allow-raw: identifier comes from the validated code definition; the user value is bound with literal wildcards.
            'contains' => is_string($value) && mb_strlen($value) <= 200 ? $builder->whereRaw($builder->getGrammar()->wrap($source)." LIKE ? ESCAPE '!'", ['%'.strtr($value, ['!' => '!!', '%' => '!%', '_' => '!_']).'%']) : throw new InvalidArgumentException('contains requires text.'),
            default => $scalar($value) ? $builder->where($source, $operator === '!=' ? '<>' : $operator, $value) : throw new InvalidArgumentException('Comparison requires a scalar value.'),
        };
    }

    /** @param array<string, array{type: string, source?: string}> $columns */
    private function source(string $name, array $columns): string
    {
        return $columns[$name]['source'] ?? $name;
    }
}
