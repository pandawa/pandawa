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

namespace Pandawa\Component\Ddd\Relationship;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Pandawa\Component\Ddd\AbstractModel;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ModelRelationsTrait
{
    /**
     * {@inheritdoc}
     */
    protected function newBelongsToMany(Builder $query, Eloquent $parent, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName = null)
    {
        if ($parent instanceof AbstractModel) {
            return new BelongsToMany(
                $query, $parent, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName
            );
        }

        return parent::newBelongsToMany(
            $query,
            $parent,
            $table,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey,
            $relatedKey,
            $relationName
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function newHasOne(Builder $query, Eloquent $parent, $foreignKey, $localKey)
    {
        if ($parent instanceof AbstractModel) {
            return new HasOne($query, $parent, $foreignKey, $localKey);
        }

        return parent::newHasOne($query, $parent, $foreignKey, $localKey);
    }

    /**
     * {@inheritdoc}
     */
    protected function newBelongsTo(Builder $query, Eloquent $child, $foreignKey, $ownerKey, $relation)
    {
        if ($child instanceof AbstractModel) {
            return new BelongsTo($query, $child, $foreignKey, $ownerKey, $relation);
        }

        return parent::newBelongsTo($query, $child, $foreignKey, $ownerKey, $relation);
    }

    /**
     * {@inheritdoc}
     */
    protected function newHasMany(Builder $query, Eloquent $parent, $foreignKey, $localKey)
    {
        if ($parent instanceof AbstractModel) {
            return new HasMany($query, $parent, $foreignKey, $localKey);
        }

        return parent::newHasMany($query, $parent, $foreignKey, $localKey);
    }

    /**
     * {@inheritdoc}
     */
    protected function newMorphOne(Builder $query, Eloquent $parent, $type, $id, $localKey)
    {
        if ($parent instanceof AbstractModel) {
            return new MorphOne($query, $parent, $type, $id, $localKey);
        }

        return parent::newMorphOne($query, $parent, $type, $id, $localKey);
    }

    /**
     * {@inheritdoc}
     */
    protected function newMorphTo(Builder $query, Eloquent $parent, $foreignKey, $ownerKey, $type, $relation)
    {
        if ($parent instanceof AbstractModel) {
            return new MorphTo($query, $parent, $foreignKey, $ownerKey, $type, $relation);
        }

        return parent::newMorphTo($query, $parent, $foreignKey, $ownerKey, $type, $relation);
    }

    /**
     * {@inheritdoc}
     */
    protected function newMorphMany(Builder $query, Eloquent $parent, $type, $id, $localKey)
    {
        if ($parent instanceof AbstractModel) {
            return new MorphMany($query, $parent, $type, $id, $localKey);
        }

        return parent::newMorphMany($query, $parent, $type, $id, $localKey);
    }

    /**
     * {@inheritdoc}
     */
    protected function newRelatedInstance($class)
    {
        return new $class();
    }
}
