<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Relations;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo as EloquentBelongsTo;
use Pandawa\Component\Eloquent\Model;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BelongsTo extends EloquentBelongsTo
{
    public function associate($model): Model|Eloquent
    {
        if ($model instanceof Eloquent && $this->parent instanceof Model) {
            $this->parent->addBeforeSaveCallback(function () use ($model) {
                $model->push();
                $this->child->setAttribute($this->foreignKey, $model->getAttribute($this->ownerKey));
            });

            $this->child->setRelation($this->getRelationName(), $model);

            return $this->child;
        }

        return parent::associate($model);
    }
}
