<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Pandawa\Component\Eloquent\Relations\BelongsTo;
use Pandawa\Component\Eloquent\Relations\BelongsToMany;
use Pandawa\Component\Eloquent\Relations\HasMany;
use Pandawa\Component\Eloquent\Relations\HasOne;
use Pandawa\Component\Eloquent\Relations\MorphMany;
use Pandawa\Component\Eloquent\Relations\MorphOne;
use Pandawa\Component\Eloquent\Relations\MorphTo;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait HasRelationshipsTrait
{
    /**
     * @return BelongsTo
     */
    abstract public function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null);

    /**
     * @return HasOne
     */
    abstract public function hasOne($related, $foreignKey = null, $localKey = null);

    /**
     * @return MorphOne
     */
    abstract public function morphOne($related, $name, $type = null, $id = null, $localKey = null);

    /**
     * @return MorphTo
     */
    abstract public function morphTo($name = null, $type = null, $id = null, $ownerKey = null);

    /**
     * @return HasMany
     */
    abstract public function hasMany($related, $foreignKey = null, $localKey = null);

    /**
     * @return MorphMany
     */
    abstract public function morphMany($related, $name, $type = null, $id = null, $localKey = null);

    /**
     * @return BelongsToMany
     */
    abstract public function belongsToMany(
        $related,
        $table = null,
        $foreignPivotKey = null,
        $relatedPivotKey = null,
        $parentKey = null,
        $relatedKey = null,
        $relation = null
    );

    protected function newBelongsTo(Builder $query, Model $child, $foreignKey, $ownerKey, $relation): BelongsTo
    {
        return new BelongsTo($query, $child, $foreignKey, $ownerKey, $relation);
    }

    protected function newBelongsToMany(
        Builder $query,
        Model $parent,
        $table,
        $foreignPivotKey,
        $relatedPivotKey,
        $parentKey,
        $relatedKey,
        $relationName = null
    ): BelongsToMany {
        return new BelongsToMany(
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

    protected function newHasMany(Builder $query, Model $parent, $foreignKey, $localKey): HasMany
    {
        return new HasMany($query, $parent, $foreignKey, $localKey);
    }

    protected function newHasOne(Builder $query, Model $parent, $foreignKey, $localKey): HasOne
    {
        return new HasOne($query, $parent, $foreignKey, $localKey);
    }

    protected function newMorphMany(Builder $query, Model $parent, $type, $id, $localKey): MorphMany
    {
        return new MorphMany($query, $parent, $type, $id, $localKey);
    }

    protected function newMorphOne(Builder $query, Model $parent, $type, $id, $localKey): MorphOne
    {
        return new MorphOne($query, $parent, $type, $id, $localKey);
    }

    protected function newMorphTo(Builder $query, Model $parent, $foreignKey, $ownerKey, $type, $relation): MorphTo
    {
        return new MorphTo($query, $parent, $foreignKey, $ownerKey, $type, $relation);
    }
}
