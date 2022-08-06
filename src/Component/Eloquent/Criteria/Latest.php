<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Criteria;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Pandawa\Contracts\Eloquent\CriterionInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Latest implements CriterionInterface
{
    public function __construct(protected readonly string $column = 'created_at')
    {
    }

    public function apply(QueryBuilder|EloquentBuilder $query): void
    {
        $query->latest($this->column);
    }
}
