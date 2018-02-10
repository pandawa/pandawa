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

namespace Pandawa\Component\Ddd;

use Pandawa\Component\Identifier\IdentifierInterface;
use Pandawa\Component\Serializer\SerializableInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use ReflectionClass;
use ReflectionMethod;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractRepository
{
    /**
     * @var ReflectionClass
     */
    private $reflection;

    /**
     * @var AbstractModel[]
     */
    private $queuing = [];

    /**
     * @var int
     */
    private $pageSize;

    /**
     * Constructor.
     *
     * @throws \ReflectionException
     */
    public function __construct()
    {
        $this->reflection = new ReflectionClass(get_called_class());
    }

    /**
     * @param IdentifierInterface $id
     * @param int|null            $lockMode
     *
     * @return AbstractModel|mixed|null
     */
    public function find(IdentifierInterface $id, int $lockMode = null): ?AbstractModel
    {
        if ($id instanceof SerializableInterface) {
            $id = $id->serialize();
        }

        $qb = $this->createQueryBuilder();

        switch ($lockMode) {
            case LockModes::PESSIMISTIC_WRITE:
                $qb->lockForUpdate();
                break;
            case LockModes::PESSIMISTIC_READ:
                $qb->sharedLock();
                break;
        }

        return $qb->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->execute($this->createQueryBuilder());
    }

    /**
     * Perform save model.
     *
     * @param AbstractModel $model
     *
     * @throws \ReflectionException
     */
    public function save(AbstractModel $model): void
    {
        if ($model instanceof AbstractModel) {
            $this->persist($model);

            return;
        }

        throw new \InvalidArgumentException(sprintf('Model should instance of "%s"', AbstractModel::class));
    }

    /**
     * @return Builder|QueryBuilder
     */
    protected function createQueryBuilder()
    {
        $model = $this->createModel();
        $queryBuilder = $model->newQuery();

        return $queryBuilder;
    }

    /**
     * Get model class.
     *
     * @return string
     */
    protected function getModelClass(): string
    {
        $fullName = $this->reflection->getName();
        $className = $this->reflection->getShortName();
        $className = substr($className, 0, strpos($className, 'Repository'));
        $namespace = substr($fullName, 0, strrpos($fullName, '\\'));
        $namespace = str_replace('Repository', 'Model', $namespace);

        return sprintf('%s\\%s', $namespace, $className);
    }

    /**
     * @param Builder|QueryBuilder $query
     *
     * @return Collection|LengthAwarePaginator|AbstractModel[]|mixed
     */
    protected function execute($query)
    {
        if (null !== $this->pageSize) {
            return $query->paginate($this->pageSize);
        }

        return $query->get();
    }

    /**
     * Create model.
     *
     * @return AbstractModel
     */
    private function createModel(): AbstractModel
    {
        $modelClass = $this->getModelClass();

        return new $modelClass;
    }

    /**
     * Cascade persist model.
     *
     * @param AbstractModel $model
     * @param string        $walker
     *
     * @return bool
     * @throws \ReflectionException
     */
    private function persist(AbstractModel $model, string $walker = null): bool
    {
        if (null === $walker) {
            $walker = uniqid();
            $this->queuing = [];
        }

        $saved = false;
        if (empty($model->getKey())) {
            if (!$this->invokeSaveModel($model)) {
                return false;
            }

            $saved = true;
        }

        $this->queuing[$walker][spl_object_hash($model)] = true;
        foreach ($model->getRelations() as $entities) {
            $entities = $entities instanceof Collection ? $entities->all() : [$entities];

            foreach (array_filter($entities) as $item) {
                if (isset($this->queuing[$walker][spl_object_hash($item)])) {
                    $this->invokeSaveModel($item);

                    continue;
                }

                if ($item instanceof AbstractModel && !$this->persist($item, $walker)) {
                    return false;
                }
            }
        }

        if (null === $walker) {
            unset($this->queuing[$walker]);
        }

        if (!$saved && !$this->invokeSaveModel($model)) {
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
     * @throws \ReflectionException
     */
    private function invokeSaveModel(AbstractModel $model): bool
    {
        $method = new ReflectionMethod(get_class($model), 'persist');
        $method->setAccessible(true);

        return $method->invoke($model);
    }
}
