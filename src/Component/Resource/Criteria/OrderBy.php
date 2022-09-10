<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Criteria;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Pandawa\Contracts\Eloquent\CriterionInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class OrderBy implements CriterionInterface
{
    protected string $orderParam = 'order';

    public function __construct(
        protected readonly array $orderFields,
        protected readonly Request $request,
        protected readonly array $defaultOrder = [],
    ) {
    }

    public function apply(QueryBuilder|EloquentBuilder $query): void
    {
        if (!empty($this->orderFields) && !empty($orders = $this->getOrders())) {
            foreach ($orders as $key => $direction) {
                if (!in_array($key, $this->orderFields)) {
                    throw new InvalidArgumentException(
                        sprintf('Order by field "%s" is not allowed.', $key)
                    );
                }

                $query->orderBy($key, $this->validateSort($direction ?? 'asc'));
            }
        }
    }

    protected function getOrders(): array
    {
        return $this->request->query($this->orderParam, $this->defaultOrder);
    }

    protected function validateSort(string $sort): string
    {
        if (!in_array($sort, ['asc', 'desc'])) {
            throw new InvalidArgumentException(
                sprintf('Invalid order direction "%s".', $sort)
            );
        }

        return $sort;
    }
}
