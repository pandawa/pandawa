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

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection as LaravelCollection;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ModelSerializationTrait
{
    /**
     * Perform serialize.
     *
     * @param array $parents
     *
     * @return array|mixed
     */
    protected function serialize(array $parents)
    {
        $hash = spl_object_hash($this);

        if (isset($parents[$hash])) {
            return $this->getKey();
        }

        $parents[spl_object_hash($this)] = true;

        $attributes = $this->attributesToArray();
        $attributes = array_merge($attributes, $this->serializeRelationship($parents));

        return $attributes;
    }

    /**
     * Perform serialize relationships.
     *
     * @param array $parents
     *
     * @return array
     */
    protected function serializeRelationship(array $parents): array
    {
        $attributes = [];

        foreach ($this->getArrayableRelations() as $key => $value) {
            if (!$value || isset($parents[spl_object_hash($value)])) {
                continue;
            }

            $temp = null;

            if ($value instanceof LaravelCollection) {
                $temp = [];
                $value->each(
                    function ($model) use (&$temp, $parents, $value) {
                        if ($model instanceof AbstractModel) {
                            $temp[] = $model->serialize(array_merge($parents, [spl_object_hash($value) => true]));

                            return;
                        }

                        if ($model instanceof Arrayable) {
                            $temp[] = $model->toArray();

                            return;
                        }

                        $temp[] = $model;
                    }
                );
            } else {
                if ($value instanceof AbstractModel) {
                    $temp = $value->serialize($parents);
                } else {
                    if ($value instanceof Arrayable) {
                        $temp = parent::toArray();
                    }
                }
            }

            $attributes[$key] = $temp;
        }

        return $attributes;
    }
}
