<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Relations;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany as EloquentHasMany;
use Pandawa\Component\Eloquent\Model;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class HasMany extends EloquentHasMany
{
    public function add(Eloquent $model): void
    {
        if ($this->parent instanceof Model) {
            $this->parent->addAfterSaveCallback(function () use ($model) {
                $this->setForeignAttributesForCreate($model);
                $model->push();
            });

            return;
        }

        $this->sendBadMethodCallException();
    }

    public function remove(Eloquent $model): void
    {
        if ($this->parent instanceof Model) {
            $this->parent->addBeforeSaveCallback(function () use ($model) {
                $model->delete();
            });

            return;
        }

        $this->sendBadMethodCallException();
    }

    protected function sendBadMethodCallException(): void
    {
        throw new BadMethodCallException(
            sprintf(
                'Model "%s" should instance of "%s".',
                get_class($this->parent),
                Model::class
            )
        );
    }
}
