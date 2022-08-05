<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Criteria;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Pandawa\Contracts\Eloquent\CriterionInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Eager implements CriterionInterface
{
    public function __construct(public readonly array $relations)
    {
    }

    public function apply(QueryBuilder|EloquentBuilder $query): void
    {
        $query->with($this->relations);
    }
}
