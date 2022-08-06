<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Criteria;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Pandawa\Contracts\Eloquent\CriterionInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class OrderBy implements CriterionInterface
{
    public function __construct(protected readonly string $column, protected string $sortBy = 'asc')
    {
    }

    public function apply(QueryBuilder|EloquentBuilder $query): void
    {
        $query->orderBy($this->column, $this->sortBy);
    }
}
