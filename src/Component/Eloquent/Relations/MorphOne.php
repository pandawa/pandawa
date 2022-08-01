<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Relations;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphOne as EloquentMorphOne;
use Pandawa\Component\Eloquent\Model;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MorphOne extends EloquentMorphOne
{
    public function associate(Eloquent $model): void
    {
        if ($this->parent instanceof Model) {
            $this->parent->addAfterSaveCallback(function () use ($model) {
                $this->setForeignAttributesForCreate($model);
                $model->push();
            });
        }
    }
}
