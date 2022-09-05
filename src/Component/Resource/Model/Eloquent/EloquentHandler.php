<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Model\Eloquent;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Pandawa\Component\Eloquent\Criteria\Criteria;
use Pandawa\Component\Eloquent\Model;
use Pandawa\Component\Transformer\EloquentTransformer;
use Pandawa\Contracts\Eloquent\Factory\RepositoryFactoryInterface;
use Pandawa\Contracts\Eloquent\RepositoryInterface;
use Pandawa\Contracts\Resource\Model\HandlerInterface;
use Pandawa\Contracts\Transformer\TransformerInterface;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class EloquentHandler implements HandlerInterface
{
    use RelationsTrait,
        PersistentTrait;

    protected readonly string $model;
    protected readonly RepositoryInterface $repository;

    public function __construct(
        protected readonly RepositoryFactoryInterface $repositoryFactory,
        protected readonly Container $container,
    ) {
    }

    public function setModel(string $model): static
    {
        $this->model = $model;
        $this->repository = $this->createRepository($model);

        return $this;
    }

    public function getModelKey(): string
    {
        return $this->model::resourceName;
    }

    public function store(array $data): Model
    {
        return $this->persist(new $this->model, $data);
    }

    public function update(int|string $id, array $data): object
    {
        return $this->persist($this->findOrFails($id), $data);
    }

    public function delete(int|string $id): object
    {
        return tap($this->findOrFails($id), function (Model $model) {
            $this->repository->delete($model);
        });
    }

    public function findById(int|string $id): Model
    {
        if (null !== $model = $this->repository->findById($id)) {
            return $model;
        }

        throw new ModelNotFoundException(
            sprintf('Resource "%s" with id "%s" is not found.', $this->model::resourceName(), $id)
        );
    }

    public function find(array $options = []): LengthAwarePaginator|iterable
    {
        if (false !== $paginate = $options['paginate'] ?? false) {
            return $this->repository->paginate($paginate);
        }

        return $this->repository->findAll();
    }

    public function loadRelations(object $resource, array $relations): object
    {
        if ($resource instanceof Model) {
            $resource->load($relations);
        }

        return $resource;
    }

    public function withEager(array $relations): static
    {
        $this->repository->withRelations($relations);

        return $this;
    }

    public function withCriteria(array $criteria): static
    {
        $this->repository->withCriteria($criteria);

        return $this;
    }

    public function withFilter(array $criteria): static
    {
        /** @var Model $model */
        $model = new $this->model;
        $filters = [];

        foreach ($criteria as $key => $value) {
            $relation = Str::camel($key);

            if ($model->hasRelation($relation)) {
                $relation = $model->{$relation}();

                if (method_exists($relation, 'getForeignKeyName')) {
                    $key = $relation->getForeignKeyName();
                }
            }

            $filters[$key] = $value;
        }

        $this->repository->withCriteria(new Criteria($filters));

        return $this;
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    public function getDefaultTransformer(): TransformerInterface
    {
        return new EloquentTransformer();
    }

    protected function findOrFails(string|int $id): Model
    {
        if (null !== $object = $this->findById($id)) {
            return $object;
        }

        throw new ModelNotFoundException(
            ucfirst(sprintf(
                '"%s" with id "%s" not found.',
                $this->getModelHumanName($this->model),
                (string) $id
            ))
        );
    }

    protected function getModelHumanName(string $model): string
    {
        $reflection = new ReflectionClass($model);
        $parts = explode('_', Str::snake($reflection->getName()));

        return implode(' ', $parts);
    }

    protected function createRepository(string $model): RepositoryInterface
    {
        $alias = sprintf('Eloquent.%s', $model);

        if ($this->container->has($alias)) {
            return $this->container->get($alias);
        }

        return $this->repositoryFactory->create($model);
    }
}
