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

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Pandawa\Component\Ddd\Relationship\BelongsTo;
use Pandawa\Component\Ddd\Relationship\BelongsToMany;
use Pandawa\Component\Ddd\Relationship\HasMany;
use Pandawa\Component\Ddd\Relationship\HasOne;
use Pandawa\Component\Ddd\Relationship\MorphMany;
use Pandawa\Component\Ddd\Relationship\MorphOne;
use Pandawa\Component\Ddd\Relationship\MorphTo;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait RelationshipBehaviorTrait
{
    /**
     * @param string $related
     * @param string $foreignKey
     * @param string $otherKey
     * @param string $relation
     *
     * @return BelongsTo
     */
    abstract public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null);

    /**
     * @param string $related
     * @param string $foreignKey
     * @param string $localKey
     *
     * @return HasOne
     */
    abstract public function hasOne($related, $foreignKey = null, $localKey = null);

    /**
     * @param string $related
     * @param string $name
     * @param string $type
     * @param string $id
     * @param string $localKey
     *
     * @return MorphOne
     */
    abstract public function morphOne($related, $name, $type = null, $id = null, $localKey = null);

    /**
     * @param string $name
     * @param string $type
     * @param string $id
     *
     * @return MorphTo
     */
    abstract public function morphTo($name = null, $type = null, $id = null);

    /**
     * @param string $related
     * @param string $foreignKey
     * @param string $localKey
     *
     * @return HasMany
     */
    abstract public function hasMany($related, $foreignKey = null, $localKey = null);

    /**
     * @param string $related
     * @param string $name
     * @param string $type
     * @param string $id
     * @param string $localKey
     *
     * @return MorphMany
     */
    abstract public function morphMany($related, $name, $type = null, $id = null, $localKey = null);

    /**
     * @param string $related
     * @param string $table
     * @param string $foreignPivotKey
     * @param string $relatedPivotKey
     * @param string $parentKey
     * @param string $relatedKey
     * @param string $relation
     *
     * @return BelongsToMany
     */
    abstract public function belongsToMany($related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null, $parentKey = null, $relatedKey = null, $relation = null);

    /**
     * @param string $related
     * @param string $name
     * @param string $table
     * @param string $foreignPivotKey
     * @param string $relatedPivotKey
     * @param string $parentKey
     * @param string $relatedKey
     * @param bool   $inverse
     *
     * @return MorphToMany
     */
    abstract public function morphToMany($related, $name, $table = null, $foreignPivotKey = null, $relatedPivotKey = null, $parentKey = null, $relatedKey = null, $inverse = false);

    /**
     * @param string $related
     * @param string $name
     * @param string $table
     * @param string $foreignPivotKey
     * @param string $relatedPivotKey
     * @param string $parentKey
     * @param string $relatedKey
     *
     * @return MorphToMany
     */
    abstract public function morphedByMany($related, $name, $table = null, $foreignPivotKey = null, $relatedPivotKey = null, $parentKey = null, $relatedKey = null);
}
