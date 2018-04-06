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

use Pandawa\Component\Serializer\DeserializableInterface;
use Pandawa\Component\Serializer\SerializableInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as LaravelCollection;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ModelAttributeTrait
{
    /**
     * Set attribute to model.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $key = snake_case($key);

        parent::setAttribute($key, $value);

        if ($this->hasCast($key)) {
            $value = $this->castToValue($this->getCasts()[$key], $this->getAttribute($key));
            if (is_array($value)) {
                $value = json_encode($value);
            }

            $this->attributes[$key] = $value;
        }

        if ($value instanceof SerializableInterface) {
            $this->attributes[$key] = $value->serialize();
        }

        return $this;
    }

    /**
     * Get attribute from model.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        return parent::getAttribute(snake_case($key));
    }

    /**
     * Get relation value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getRelationValue($key)
    {
        return parent::getRelationValue(camel_case($key));
    }

    /**
     * Add cast attributes.
     *
     * @param array $attributes
     * @param array $mutatedAttributes
     *
     * @return array
     */
    protected function addCastAttributesToArray(array $attributes, array $mutatedAttributes): array
    {
        foreach ($this->getCasts() as $key => $value) {
            if (!array_key_exists($key, $attributes) || in_array($key, $mutatedAttributes)) {
                continue;
            }

            $attributes[$key] = $this->castAttribute($key, $attributes[$key]);

            if ($this->hasCast($key)) {
                $attributes[$key] = $this->getAttribute($key);
            }

            $attributes[$key] = $this->castToValue($value, $attributes[$key]);
        }

        return $attributes;
    }

    /**
     * Perform cast attribute.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Carbon|int|mixed
     */
    protected function castAttribute($key, $value)
    {
        if (is_null($value)) {
            return $value;
        }

        switch ($this->getCastType($key)) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return (float) $value;
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'object':
                return $this->fromJson($value, true);
            case 'array':
            case 'json':
                return $this->fromJson($value);
            case 'collection':
                return new LaravelCollection($this->fromJson($value));
            case 'date':
                return $this->asDate($value);
            case 'datetime':
                return $this->asDateTime($value);
            case 'timestamp':
                return $this->asTimestamp($value);
            default:
                if (null !== $value && $this->hasCast($key) && !is_object($value)) {
                    if (in_array(DeserializableInterface::class, class_implements($this->getCasts()[$key]))) {
                        return $this->getCasts()[$key]::{'deserialize'}($value);
                    }
                }

                return $value;
        }
    }

    /**
     * Cast to value.
     *
     * @param string $cast
     * @param mixed  $value
     *
     * @return mixed|string
     */
    private function castToValue(string $cast, $value)
    {
        if ($value && ($cast === 'date' || $cast === 'datetime')) {
            return $this->serializeDate($value);
        }

        if ($value instanceof SerializableInterface) {
            return $value->serialize();
        }

        return $value;
    }
}
