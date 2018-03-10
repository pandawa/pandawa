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
    protected $uncommittedActions = [];

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
    public function addPendingAction(callable $action): void
    {
        $this->uncommittedActions[] = $action;
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

    /**
     * @param array $entities
     *
     * @return Collection
     */
    public function newCollection(array $entities = []): Collection
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
        $model = parent::save($options);
        $actions = $this->uncommittedActions;

        $this->uncommittedActions = [];

        foreach ($actions as $action) {
            $action();
        }

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
}
