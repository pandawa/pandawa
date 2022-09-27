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
class Filter implements CriterionInterface
{
    protected string $filterParam = 'filter';

    public function __construct(
        protected readonly array $filters,
        protected readonly Request $request,
        protected readonly array $defaultFilters = [],
        protected readonly array $skips = [],
    ) {
    }

    public function apply(QueryBuilder|EloquentBuilder $query): void
    {
        if (!empty($this->filters) && !empty($params = $this->getFilters())) {
            $keysFilter = array_keys($this->filters);

            foreach ($params as $key => $value) {
                if (in_array($key, $this->skips)) {
                    return;
                }

                if (!in_array($key, $keysFilter)) {
                    throw new InvalidArgumentException(
                        sprintf('Filter by field "%s" is not allowed.', $key)
                    );
                }

                $filter = $this->filters[$key];
                if (is_string($filter)) {
                    $filter = [
                        'operator' => $filter,
                        'type' => 'string',
                    ];
                }

                $methodName = $this->getMethodName($filter['operator']);

                $this->{$methodName}($query, $key, $this->cast($filter['type'], $value));
            }
        }
    }

    protected function getMethodName(string $operator): string
    {
        $method = 'apply' . ucfirst($operator) . 'Filter';

        if (!method_exists($this, $method)) {
            throw new InvalidArgumentException(
                sprintf('Filter with operator "%s" is not supported.', $operator)
            );
        }

        return $method;
    }

    protected function cast(string $type, mixed $value): mixed
    {
        return match($type) {
            'int' => (int) $value,
            'float' => (float) $value,
            default => $value,
        };
    }

    protected function getFilters(): array
    {
        return $this->request->query($this->filterParam, $this->defaultFilters);
    }

    protected function applyExactFilter(QueryBuilder|EloquentBuilder $query, string $key, mixed $value): void
    {
        $query->where($key, '=', $value);
    }

    protected function applyContainsFilter(QueryBuilder|EloquentBuilder $query, string $key, mixed $value): void
    {
        $query->where($key, 'ilike', '%' . $value . '%');
    }

    protected function applyGreaterThanFilter(QueryBuilder|EloquentBuilder $query, string $key, mixed $value): void
    {
        $query->where($key, '>', $value);
    }

    protected function applyGreaterThanEqualFilter(QueryBuilder|EloquentBuilder $query, string $key, mixed $value): void
    {
        $query->where($key, '>=', $value);
    }

    protected function applyLowerThanFilter(QueryBuilder|EloquentBuilder $query, string $key, mixed $value): void
    {
        $query->where($key, '<', $value);
    }

    protected function applyLowerThanEqualFilter(QueryBuilder|EloquentBuilder $query, string $key, mixed $value): void
    {
        $query->where($key, '<=', $value);
    }
}
