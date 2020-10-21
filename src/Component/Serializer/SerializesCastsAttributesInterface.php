<?php

declare(strict_types=1);

namespace Pandawa\Component\Serializer;

use Illuminate\Database\Eloquent\Model;

/**
 * @author  Aldi Arief <aldiarief598@gmail.com>
 */
interface SerializesCastsAttributesInterface
{
    /**
     * @param Model  $model
     * @param string $key
     * @param mixed  $value
     * @param array  $attributes
     * @return mixed
     */
    public function serializeCast($model, string $key, $value, array $attributes);
}
