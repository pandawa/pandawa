<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Pandawa\Component\Eloquent\Criteria\Criteria;
use Pandawa\Component\Eloquent\Criteria\EagerLoad;
use Pandawa\Component\Eloquent\Criteria\Lock;
use Pandawa\Contracts\Eloquent\Cache\CacheableInterface;
use Pandawa\Contracts\Eloquent\CriterionInterface;
use Pandawa\Contracts\Eloquent\LockMode;
use Pandawa\Contracts\Eloquent\Persistent\PersistentInterface;
use Pandawa\Contracts\Eloquent\QueryBuilderInterface;
use Pandawa\Contracts\Eloquent\RepositoryInterface;

/**
 * This class provides ability to load models and persistent.
 *
 * @template TModel
 * @implements RepositoryInterface<TModel>
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Repository implements RepositoryInterface
{
    protected readonly Model $model;

    public function __construct(
        protected readonly QueryBuilderInterface $query,
        protected readonly PersistentInterface $persistent,
    ) {
        $this->model = $this->query->getModel();

        if ($this instanceof CacheableInterface) {
            $this->query->enableCache();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function withRelations(array $relations): static
    {
        $this->query->withCriteria(new EagerLoad($relations));

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function withCriteria(CriterionInterface|array $criteria): static
    {
        $this->query->withCriteria($criteria);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function withLock(LockMode $lockMode): static
    {
        $this->query->withCriteria(new Lock($lockMode));

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function findById(int|string $id): ?Model
    {
        return $this->query->findByKey($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria): ?Model
    {
        return $this->query->withCriteria(new Criteria($criteria))->first();
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria): Collection
    {
        return $this->query->withCriteria(new Criteria($criteria))->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(): Collection
    {
        return $this->query->get();
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(int $pageSize): LengthAwarePaginator
    {
        return $this->query->paginate($pageSize);
    }

    /**
     * {@inheritDoc}
     */
    public function save(Model $model): Model
    {
        return $this->persistent->save($model);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(Model $model): Model
    {
        return $this->persistent->delete($model);
    }
}
