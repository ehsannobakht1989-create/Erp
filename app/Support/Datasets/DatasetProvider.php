<?php

declare(strict_types=1);

namespace App\Support\Datasets;

use Illuminate\Database\Query\Builder;

/**
 * Module-owned read-only dataset. The registry adds company, permission, row-scope,
 * column, filter, sort and paging enforcement around the base query.
 */
interface DatasetProvider
{
    /**
     * @return array{
     *     permission: string,
     *     company_column?: string,
     *     columns: array<string, array{type: 'string'|'int'|'decimal'|'date'|'datetime'|'bool', source?: string}>,
     *     scope_columns?: array{branch?: string, department?: string, own?: string}
     * }
     */
    public function definition(): array;

    public function baseQuery(): Builder;
}
