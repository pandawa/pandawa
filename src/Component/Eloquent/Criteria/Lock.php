<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Criteria;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Pandawa\Contracts\Eloquent\CriterionInterface;
use Pandawa\Contracts\Eloquent\LockMode;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Lock implements CriterionInterface
{
    public function __construct(protected readonly LockMode $lockMode)
    {
    }

    public function apply(QueryBuilder|EloquentBuilder $query): void
    {
        match ($this->lockMode) {
            LockMode::PESSIMISTIC_READ => $query->sharedLock(),
            LockMode::PESSIMISTIC_WRITE => $query->lockForUpdate(),
        };
    }
}
