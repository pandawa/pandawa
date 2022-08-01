<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Relations;

use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentBelongsToMany;
use Pandawa\Component\Eloquent\Model;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BelongsToMany extends EloquentBelongsToMany
{
    public function attach($id, array $attributes = [], $touch = true): void
    {
        if ($this->parent instanceof Model) {
            $this->parent->addAfterSaveCallback(function () use ($id, $attributes, $touch) {
                parent::attach($id, $attributes, $touch);
            });

            return;
        }

        parent::attach($id, $attributes, $touch);
    }

    public function detach($ids = null, $touch = true): void
    {
        if ($this->parent instanceof Model) {
            $this->parent->addAfterSaveCallback(function () use ($ids, $touch) {
                parent::detach($ids, $touch);
            });

            return;
        }

        parent::detach($ids, $touch);
    }

    public function sync($ids, $detaching = true): void
    {
        if ($this->parent instanceof Model) {
            $this->parent->addAfterSaveCallback(function () use ($ids, $detaching) {
                parent::sync($ids, $detaching);
            });

            return;
        }

        parent::sync($ids, $detaching);
    }

    public function updateExistingPivot($id, array $attributes, $touch = true): void
    {
        if ($this->parent instanceof Model) {
            $this->parent->addAfterSaveCallback(function () use ($id, $attributes, $touch) {
                parent::updateExistingPivot($id, $attributes, $touch);
            });

            return;
        }

        parent::updateExistingPivot($id, $attributes, $touch);
    }
}
