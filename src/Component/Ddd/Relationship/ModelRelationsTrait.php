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
use Pandawa\Component\Ddd\AbstractModel;
use Illuminate\Database\Eloquent\Model as Eloquent;

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
    protected function newRelatedInstance($class)
    {
        return new $class();
    }
}
