<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder;
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

    public function cursor(): LazyCollection
    {
        return tap($this->queryBuilder->cursor(), function () {
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
            return tap($result, function() {
                $this->refresh();
            });
        }

        $query = $this;

        return tap($this->queryBuilder->first(), function (?Model $model) use ($query) {
            if (null !== $model && $this->isCacheEnabled()) {
                $this->cacheHandler?->rememberQuery($query, $model);
            }

            $this->refresh();
        });
    }

    public function __call(string $method, array $args): mixed
    {
        return $this->queryBuilder->{$method}(...$args);
    }

    protected function refresh(): void
    {
        $this->setModel($this->model);
    }
}
