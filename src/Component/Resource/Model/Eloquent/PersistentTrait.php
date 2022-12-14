<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Model\Eloquent;

use Pandawa\Component\Eloquent\Model;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait PersistentTrait
{
    protected function persist(Model $model, array $data): Model
    {
        $data = $this->appendRelations($model, $data);

        return tap($model->fill($data), function (Model $model) {
            $this->repository->save($model);
        });
    }
}
