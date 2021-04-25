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

use Exception;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Pandawa\Component\Ddd\Relationship\ModelRelationsTrait;
use ReflectionException;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractModel extends Eloquent implements Model
{
    use ModelUuidTrait,
        ModelRelationsTrait,
        ModelAttributeTrait,
        ModelSerializationTrait;

    protected static $repositoryClass;
    protected static $modelClass;
    protected static $resourceName;

    /**
     * @var array
     */
    protected $uncommittedActions = [
        'before' => [],
        'after'  => [],
    ];

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $hidden = ['pivot'];

    /**
     * Constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (true === $this->enableUuid) {
            $this->keyType = 'string';
        }

        parent::__construct($attributes);
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    public static function getRepositoryClass(): ?string
    {
        if (null === $repositoryClass = static::$repositoryClass) {
            $modelClass = static::getModelClass();
            $repositoryClass = str_replace('Model', 'Repository', $modelClass);
            $repositoryClass = sprintf('%sRepository', $repositoryClass);
        }

        if (class_exists($repositoryClass)) {
            return $repositoryClass;
        }

        return null;
    }

    public static function getResourceName(): string
    {
        if (null !== $resource = static::$resourceName) {
            return $resource;
        }

        $modelClass = static::getModelClass();
        $name = substr($modelClass, (int)strrpos($modelClass, '\\') + 1);

        return Str::snake($name);
    }

    public static function getMappedClass(): string
    {
        return static::getModelClass();
    }

    public function getMappedModel()
    {
        return $this;
    }

    public function hasRelation(string $method): bool
    {
        return method_exists($this, $method) && $this->{$method}() instanceof Relation;
    }

    public static function getModelClass(): ?string
    {
        if (null !== $modelClass = static::$modelClass) {
            return $modelClass;
        }

        return get_called_class();
    }

    /**
     * @param callable $action
     */
    public function addAfterAction(callable $action): void
    {
        $this->uncommittedActions['after'][] = $action;
    }

    /**
     * @param callable $action
     */
    public function addBeforeAction(callable $action): void
    {
        $this->uncommittedActions['before'][] = $action;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getKey();
    }

    /**
     * @param array $options
     *
     * @deprecated
     */
    public function save(array $options = []): void
    {
        throw new RuntimeException('Forbidden save directly from model. Please use repository instead.');
    }

    /**
     * @deprecated
     */
    public function delete(): void
    {
        throw new RuntimeException('Forbidden delete directly from model. Please use repository instead.');
    }

    public function touch(): void
    {
        if ($this->usesTimestamps()) {
            $this->updateTimestamps();
        }
    }

    /**
     * @param array $entities
     *
     * @return CollectionInterface
     */
    public function newCollection(array $entities = []): CollectionInterface
    {
        return new Collection($entities);
    }

    /**
     * Cast model to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->serialize([]);
    }

    /**
     * Perform persist model.
     *
     * @param array $options
     *
     * @return bool
     */
    protected function persist(array $options = []): bool
    {
        $this->executeActions('before');

        $model = parent::save($options);

        $this->executeActions('after');

        return $model;
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function remove(): bool
    {
        return parent::delete();
    }

    /**
     * @param string $type
     */
    private function executeActions(string $type): void
    {
        $actions = $this->uncommittedActions[$type];

        $this->uncommittedActions[$type] = [];

        foreach ($actions as $action) {
            $action();
        }
    }
}
