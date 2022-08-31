<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Model\Eloquent;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Pandawa\Component\Eloquent\Model;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait RelationsTrait
{
    protected function appendRelations(Model $model, array $data): array
    {
        foreach ($data as $attribute => $value) {
            $method = Str::camel($attribute);

            if ($model->hasRelation($method)) {
                $relation = $model->{$method}();

                if ($relation instanceof BelongsToMany) {
                    $relation->detach();
                    if (!empty($value)) {
                        $relation->attach((array)$value);
                    }
                } else {
                    if (null !== $value) {
                        $relation = $this->findRelatedModel($model->{$method}(), $value);
                    } else {
                        $relation = null;
                    }

                    $model->{$method}()->associate($relation);
                }

                unset($data[$attribute]);
            }
        }

        return $data;
    }

    protected function findRelatedModel(Relation $relation, array|string|int $value): Model
    {
        $class = get_class($relation->getModel());

        if (is_array($value)) {
            $key = $relation->getModel()->getKeyName();

            if (null !== $id = array_get($value, $key)) {
                return $this->persist($class::{'findOrFail'}($id), array_except($value, $key));
            }

            return $this->persist(new $class(), $value);
        }

        return $class::{'findOrFail'}($value);
    }
}
