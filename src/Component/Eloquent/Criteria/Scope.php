<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Criteria;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Pandawa\Contracts\Eloquent\CriterionInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Scope implements CriterionInterface
{
    public function __construct(protected readonly array $scopes)
    {
    }

    public function apply(QueryBuilder|EloquentBuilder $query): void
    {
        $query->scopes($this->scopes);
    }
}
