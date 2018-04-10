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
use Pandawa\Component\Ddd\Relationship\ModelRelationsTrait;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractModel extends Eloquent
{
    use ModelUuidTrait,
        ModelRelationsTrait,
        ModelAttributeTrait,
        ModelSerializationTrait;

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
     * @return string
     * @throws ReflectionException
     */
    public static function getRepositoryClass(): ?string
    {
        $reflection = new ReflectionClass(get_called_class());
        $modelClass = $reflection->getName();
        $repositoryClass = str_replace('Model', 'Repository', $modelClass);
        $repositoryClass = sprintf('%sRepository', $repositoryClass);

        if (class_exists($repositoryClass)) {
            return $repositoryClass;
        }

        return null;
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
