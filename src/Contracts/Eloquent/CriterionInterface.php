<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Eloquent;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface CriterionInterface
{
    public function apply(EloquentBuilder|QueryBuilder $query): void;
}
