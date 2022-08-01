<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Relations;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphTo as EloquentMorphTo;
use Pandawa\Component\Eloquent\Model;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MorphTo extends EloquentMorphTo
{
    public function associate($model): Eloquent
    {
        if ($this->parent instanceof Model) {
            $this->parent->addBeforeSaveCallback(function () use ($model) {
                $this->parent->setAttribute(
                    $this->foreignKey,
                    $model instanceof Eloquent ? $model->getKey() : null,
                );

                $this->parent->setAttribute(
                    $this->morphType,
                    $model instanceof Eloquent ? $model->getMorphClass() : null,
                );
            });

            return $this->parent->setRelation($this->getRelationName(), $model);
        }

        return parent::associate($model);
    }
}
