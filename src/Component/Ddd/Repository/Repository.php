<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Component\Ddd\Repository;

use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Pandawa\Component\Ddd\AbstractModel;
use Pandawa\Component\Ddd\Collection;
use Pandawa\Component\Ddd\LockModes;
use Pandawa\Component\Ddd\Specification\SpecificationInterface;
use Pandawa\Component\Serializer\SerializableInterface;
use ReflectionException;
use ReflectionMethod;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Repository implements RepositoryInterface
{
    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var AbstractModel[]
     */
    protected $queuing = [];

    /**
     * @var int
     */
    protected $pageSize;

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @var SpecificationInterface[]
     */
    protected $specifications = [];

    /**
     * @var int
     */
    protected $locking;

    /**
     * Constructor.
     *
     * @param string $modelClass
     */
    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    /**
     * @param int $lockMode
     */
    public function lock(int $lockMode): void
    {
        $this->locking = $lockMode;
    }

    /**
     * @param array $withs
     */
    public function with(array $withs): void
    {
        $this->relations = array_merge($this->relations, $withs);
    }

    /**
     * @param int $pageSize
     */
    public function paginate(int $pageSize): void
    {
        $this->pageSize = $pageSize;
    }

    /**
     * Match multiple specification.
     *
     * @param array $specifications
     */
    public function matches(array $specifications): void
    {
        foreach ($specifications as $specification) {
            $this->match($specification);
        }
    }

    /**
     * Match with specification.
     *
     * @param SpecificationInterface $specification
     */
    public function match(SpecificationInterface $specification): void
    {
        $this->specifications[get_class($specification)] = $specification;
    }

    /**
     * @param mixed    $id
     * @param int|null $lockMode
     *
     * @return AbstractModel|mixed|null
     */
    public function find($id, int $lockMode = null): ?AbstractModel
    {
        if ($id instanceof SerializableInterface) {
            $id = $id->serialize();
        }

        $qb = $this->createQueryBuilder();
        $qb->whereKey($id);

        switch ($lockMode) {
            case LockModes::PESSIMISTIC_WRITE:
                $qb->lockForUpdate();
                break;
            case LockModes::PESSIMISTIC_READ:
                $qb->sharedLock();
                break;
        }

        return $this->executeSingle($qb);
    }

    /**
     * @param array $criteria
     *
     * @return AbstractModel|mixed
     */
    public function findOneBy(array $criteria): ?AbstractModel
    {
        $stmt = $this->createQueryBuilder();

        foreach ($criteria as $key => $value) {
            $stmt->where($key, $value);
        }

        return $this->executeSingle($stmt);
    }

    /**
     * @param array $criteria
     *
     * @return Collection|LengthAwarePaginator|AbstractModel[]|mixed
     */
    public function findBy(array $criteria)
    {
        $stmt = $this->createQueryBuilder();

        foreach ($criteria as $key => $value) {
            $stmt->where($key, $value);
        }

        return $this->execute($stmt);
    }

    /**
     * @return LengthAwarePaginator|mixed|AbstractModel[]|Collection
     */
    public function findAll()
    {
        return $this->findBy([]);
    }

    /**
     * Perform save model.
     *
     * @param AbstractModel|mixed $model
     *
     * @throws ReflectionException
     */
    public function save(AbstractModel $model): void
    {
        DB::transaction(
            function () use ($model) {
                $this->persist($model);
            }
        );
    }

    /**
     * Perform remove model.
     *
     * @param AbstractModel $model
     *
     * @throws ReflectionException
     */
    public function remove(AbstractModel $model): void
    {
        DB::transaction(
            function () use ($model) {
                $this->invokeDeleteModel($model);
            }
        );
    }

    /**
     * @param string|null $modelClass
     *
     * @return Builder|QueryBuilder
     */
    protected function createQueryBuilder(string $modelClass = null)
    {
        $model = $this->createModel($modelClass ?: $this->modelClass);
        $queryBuilder = $model->newQuery();

        return $queryBuilder;
    }

    /**
     * @param Builder|QueryBuilder $query
     *
     * @return Collection|LengthAwarePaginator|AbstractModel[]|mixed
     */
    protected function execute($query)
    {
        $this->applyRelations($query);
        $this->applySpecifications($query);
        $this->applyLocking($query);

        if (null !== $this->pageSize) {
            $pageSize = $this->pageSize;
            $this->pageSize = null;

            return $query->paginate($pageSize);
        }

        return $query->get();
    }

    /**
     * @param Builder|QueryBuilder $query
     *
     * @return AbstractModel|mixed
     */
    protected function executeSingle($query): ?AbstractModel
    {
        $this->applyRelations($query);
        $this->applySpecifications($query);
        $this->applyLocking($query);

        return $query->first();
    }

    /**
     * @param Builder|QueryBuilder $query
     */
    protected function applySpecifications($query): void
    {
        if (!empty($this->specifications)) {
            foreach ($this->specifications as $specification) {
                $specification->match($query);
            }

            $this->specifications = [];
        }
    }

    /**
     * @param Builder|QueryBuilder $query
     */
    protected function applyRelations($query): void
    {
        if (!empty($this->relations)) {
            $query->with($this->relations);

            $this->relations = [];
        }
    }

    /**
     * @param Builder|QueryBuilder $query
     */
    protected function applyLocking($query): void
    {
        switch ($this->locking) {
            case LockModes::PESSIMISTIC_WRITE:
                $query->lockForUpdate();
                break;
            case LockModes::PESSIMISTIC_READ:
                $query->sharedLock();
                break;
        }

        $this->locking = null;
    }

    /**
     * Create model.
     *
     * @param string $modelClass
     *
     * @return AbstractModel
     */
    private function createModel(string $modelClass): AbstractModel
    {
        return new $modelClass;
    }

    /**
     * Cascade persist model.
     *
     * @param AbstractModel $model
     * @param string        $walker
     *
     * @return bool
     * @throws ReflectionException
     */
    private function persist(AbstractModel $model, string $walker = null): bool
    {
        if (null === $walker) {
            $walker = uniqid();
            $this->queuing = [];
        }

        $this->queuing[$walker][spl_object_hash($model)] = true;
        foreach ($model->getRelations() as $entities) {
            $entities = $entities instanceof Collection ? $entities->all() : [$entities];

            /** @var AbstractModel $item */
            foreach (array_filter($entities) as $item) {
                if (isset($this->queuing[$walker][spl_object_hash($item)])) {
                    $this->invokeSaveModel($item);
                }

                if ($item instanceof AbstractModel) {
                    $this->persist($item, $walker);
                }
            }
        }

        if (null === $walker) {
            unset($this->queuing[$walker]);
        }

        if (!$this->invokeSaveModel($model)) {
            return false;
        }

        return true;
    }

    /**
     * Access private method persist model.
     *
     * @param AbstractModel $model
     *
     * @return bool
     * @throws ReflectionException
     */
    private function invokeSaveModel(AbstractModel $model): bool
    {
        $method = new ReflectionMethod(get_class($model), 'persist');
        $method->setAccessible(true);

        return $method->invoke($model);
    }

    /**
     * @param AbstractModel $model
     *
     * @return bool
     * @throws ReflectionException
     */
    private function invokeDeleteModel(AbstractModel $model): bool
    {
        $method = new ReflectionMethod(get_class($model), 'remove');
        $method->setAccessible(true);

        return $method->invoke($model);
    }
}
