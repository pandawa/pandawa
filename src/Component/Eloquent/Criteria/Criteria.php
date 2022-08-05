<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Criteria;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Pandawa\Contracts\Eloquent\CriterionInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Criteria implements CriterionInterface
{
    public function __construct(protected readonly array $criteria)
    {
    }

    public function apply(QueryBuilder|EloquentBuilder $query): void
    {
        foreach ($this->criteria as $key => $value) {
            $query->where($key, $value);
        }
    }
}
