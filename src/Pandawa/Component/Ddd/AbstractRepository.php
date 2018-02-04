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
     * @var AbstractEntity[]
     */
    private $queuing = [];

    /**
     * @var int
     */
    private $pageSize;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->reflection = new ReflectionClass(get_called_class());
    }

    /**
     * @param IdentifierInterface $id
     * @param int|null            $lockMode
     *
     * @return AbstractEntity|mixed|null
     */
    public function find(IdentifierInterface $id, int $lockMode = null): ?AbstractEntity
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
     * Perform save entity.
     *
     * @param AbstractEntity $entity
     */
    public function save(AbstractEntity $entity): void
    {
        if ($entity instanceof AbstractEntity) {
            $this->persist($entity);

            return;
        }

        throw new \InvalidArgumentException(sprintf('Entity should instance of "%s"', AbstractEntity::class));
    }

    /**
     * @return Builder|QueryBuilder
     */
    protected function createQueryBuilder()
    {
        $entity = $this->createEntity();
        $queryBuilder = $entity->newQuery();

        return $queryBuilder;
    }

    /**
     * Get entity class.
     *
     * @return string
     */
    protected function getEntityClass(): string
    {
        $fullName = $this->reflection->getName();
        $className = $this->reflection->getShortName();
        $className = substr($className, 0, strpos($className, 'Repository'));
        $namespace = substr($fullName, 0, strrpos($fullName, '\\'));
        $namespace = str_replace('Repository', 'Entity', $namespace);

        return sprintf('%s\\%s', $namespace, $className);
    }

    /**
     * @param Builder|QueryBuilder $query
     *
     * @return Collection|LengthAwarePaginator|AbstractEntity[]|mixed
     */
    protected function execute($query)
    {
        if (null !== $this->pageSize) {
            return $query->paginate($this->pageSize);
        }

        return $query->get();
    }

    /**
     * Create entity.
     *
     * @return AbstractEntity
     */
    private function createEntity(): AbstractEntity
    {
        $entityClass = $this->getEntityClass();

        return new $entityClass;
    }

    /**
     * Cascade persist entity.
     *
     * @param AbstractEntity $entity
     * @param string         $walker
     *
     * @return bool
     */
    private function persist(AbstractEntity $entity, string $walker = null): bool
    {
        if (null === $walker) {
            $walker = uniqid();
            $this->queuing = [];
        }

        $saved = false;
        if (empty($entity->getKey())) {
            if (!$this->invokeSaveEntity($entity)) {
                return false;
            }

            $saved = true;
        }

        $this->queuing[$walker][spl_object_hash($entity)] = true;
        foreach ($entity->getRelations() as $entities) {
            $entities = $entities instanceof Collection ? $entities->all() : [$entities];

            foreach (array_filter($entities) as $item) {
                if (isset($this->queuing[$walker][spl_object_hash($item)])) {
                    $this->invokeSaveEntity($item);

                    continue;
                }

                if ($item instanceof AbstractEntity && !$this->persist($item, $walker)) {
                    return false;
                }
            }
        }

        if (null === $walker) {
            unset($this->queuing[$walker]);
        }

        if (!$saved && !$this->invokeSaveEntity($entity)) {
            return false;
        }

        return true;
    }

    /**
     * Access private method persist entity.
     *
     * @param AbstractEntity $entity
     *
     * @return bool
     */
    private function invokeSaveEntity(AbstractEntity $entity): bool
    {
        $method = new ReflectionMethod(get_class($entity), 'persist');
        $method->setAccessible(true);

        return $method->invoke($entity);
    }
}
