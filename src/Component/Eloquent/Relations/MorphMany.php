<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Relations;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphMany as EloquentMorphMany;
use Pandawa\Component\Eloquent\Model;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MorphMany extends EloquentMorphMany
{
    public function add(Eloquent $model): void
    {
        if ($this->parent instanceof Model) {
            $this->parent->addAfterSaveCallback(function () use ($model) {
                $this->setForeignAttributesForCreate($model);
                $model->push();
            });
        }
    }
}
