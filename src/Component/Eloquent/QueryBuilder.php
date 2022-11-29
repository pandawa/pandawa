<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as ModelCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Pandawa\Contracts\Eloquent\Cache\CacheHandlerInterface;
use Pandawa\Contracts\Eloquent\CriterionInterface;
use Pandawa\Contracts\Eloquent\QueryBuilderInterface;

/**
 * @template TModel
 * @implements QueryBuilderInterface<TModel>
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class QueryBuilder implements QueryBuilderInterface
{
    protected Builder|EloquentBuilder $queryBuilder;
    protected Model $model;
    protected bool $cacheEnabled = false;

    public function __construct(protected readonly ?CacheHandlerInterface $cacheHandler = null)
    {
    }

    protected function isCacheEnabled(): bool
    {
        return true == $this->cacheEnabled && null !== $this->cacheHandler;
    }

    public function setModel(Model $model): static
    {
        $this->model = $model;
        $this->queryBuilder = $this->model->newQuery();

        return $this;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function enableCache(bool $enabled = true): static
    {
        $this->cacheEnabled = $enabled;

        return $this;
    }

    public function withCriteria(CriterionInterface|array $criteria): static
    {
        if (is_array($criteria)) {
            foreach ($criteria as $criterion) {
                $this->withCriteria($criterion);
            }

            return $this;
        }

        $criteria->apply($this->queryBuilder);

        return $this;
    }

    public function get(array $columns = ['*']): Collection|ModelCollection
    {
        if ($this->isCacheEnabled() && null !== $results = $this->cacheHandler?->getByQuery($this)) {
            return tap($results, function () {
                $this->refresh();
            });
        }

        $query = $this;

        return tap($this->queryBuilder->get($columns), function (Collection|ModelCollection $results) use ($query) {
            if ($results->count() && $this->isCacheEnabled()) {
                $this->cacheHandler->rememberQuery($query, $results);
            }

            $this->refresh();
        });
    }

    public function findByKey(int|string $key): ?Model
    {
        if ($this->isCacheEnabled() && null !== $model = $this->cacheHandler?->getByKey($this->model, $key)) {
            return tap($model, function () {
                $this->refresh();
            });
        }

        return tap($this->queryBuilder->whereKey($key)->first(), function (?Model $model) {
            if ($model && $this->isCacheEnabled()) {
                $this->cacheHandler?->rememberModel($model);
            }

            $this->refresh();
        });
    }

    public function first(array $columns = ['*']): ?Model
    {
        if ($this->isCacheEnabled() && null !== $result = $this->cacheHandler?->getByQuery($this)) {
            return tap($result, function () {
                $this->refresh();
            });
        }

        $query = $this;

        return tap($this->queryBuilder->first($columns), function (?Model $model) use ($query) {
            if (null !== $model && $this->isCacheEnabled()) {
                $this->cacheHandler?->rememberQuery($query, $model);
            }

            $this->refresh();
        });
    }

    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null): LengthAwarePaginator
    {
        if ($this->isCacheEnabled() && null !== $results = $this->cacheHandler?->getByQuery($this)) {
            return tap($results, function () {
                $this->refresh();
            });
        }

        $query = $this;

        return tap($this->queryBuilder->paginate($perPage, $columns, $pageName, $page), function (LengthAwarePaginator $page) use ($query) {
            if ($this->isCacheEnabled()) {
                $this->cacheHandler->rememberQuery($query, $page);
            }

            $this->refresh();
        });
    }

    public function cursor(): LazyCollection
    {
        return tap($this->queryBuilder->cursor(), function () {
            $this->refresh();
        });
    }

    public function chunk($count, callable $callback): bool
    {
        return tap($this->queryBuilder->chunk($count, $callback), function () {
            $this->refresh();
        });
    }

    public function count($columns = '*'): int
    {
        if ($this->isCacheEnabled() && null !== $results = $this->cacheHandler?->getByQuery($this)) {
            return tap($results, function () {
                $this->refresh();
            });
        }

        $query = $this;

        return tap($this->queryBuilder->count($columns), function (int $count) use ($query) {
            if ($this->isCacheEnabled()) {
                $this->cacheHandler->rememberQuery($query, $count);
            }

            $this->refresh();
        });
    }

    public function min($column): mixed
    {
        if ($this->isCacheEnabled() && null !== $results = $this->cacheHandler?->getByQuery($this)) {
            return tap($results, function () {
                $this->refresh();
            });
        }

        $query = $this;

        return tap($this->queryBuilder->min($column), function (mixed $min) use ($query) {
            if ($this->isCacheEnabled()) {
                $this->cacheHandler->rememberQuery($query, $min);
            }

            $this->refresh();
        });
    }

    public function max($column): mixed
    {
        if ($this->isCacheEnabled() && null !== $results = $this->cacheHandler?->getByQuery($this)) {
            return tap($results, function () {
                $this->refresh();
            });
        }

        $query = $this;

        return tap($this->queryBuilder->max($column), function (mixed $max) use ($query) {
            if ($this->isCacheEnabled()) {
                $this->cacheHandler->rememberQuery($query, $max);
            }

            $this->refresh();
        });
    }

    public function sum($column): mixed
    {
        if ($this->isCacheEnabled() && null !== $results = $this->cacheHandler?->getByQuery($this)) {
            return tap($results, function () {
                $this->refresh();
            });
        }

        $query = $this;

        return tap($this->queryBuilder->sum($column), function (mixed $sum) use ($query) {
            if ($this->isCacheEnabled()) {
                $this->cacheHandler->rememberQuery($query, $sum);
            }

            $this->refresh();
        });
    }

    public function avg($column): mixed
    {
        if ($this->isCacheEnabled() && null !== $results = $this->cacheHandler?->getByQuery($this)) {
            return tap($results, function () {
                $this->refresh();
            });
        }

        $query = $this;

        return tap($this->queryBuilder->avg($column), function (mixed $avg) use ($query) {
            if ($this->isCacheEnabled()) {
                $this->cacheHandler->rememberQuery($query, $avg);
            }

            $this->refresh();
        });
    }

    public function average($column): mixed
    {
        return $this->avg($column);
    }

    public function __call(string $method, array $args): mixed
    {
        $result = $this->queryBuilder->{$method}(...$args);

        if ($result instanceof Builder) {
            return $this;
        }

        return $result;
    }

    protected function refresh(): void
    {
        $this->setModel($this->model);
    }
}
