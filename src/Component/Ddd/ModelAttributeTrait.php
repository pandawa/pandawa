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

use Illuminate\Support\Carbon;
use Pandawa\Component\Serializer\DeserializableInterface;
use Pandawa\Component\Serializer\SerializableInterface;

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

        if ($value instanceof SerializableInterface) {
            $this->attributes[$key] = $value->serialize();

            return $this;
        }

        return parent::setAttribute($key, $value);
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
        if (null !== $value && $this->hasCast($key) && !is_object($value)) {
            $className = $this->getCasts()[$key];
            if (class_exists($className) && in_array(DeserializableInterface::class, class_implements($className))) {
                return $this->getCasts()[$key]::{'deserialize'}($value);
            }
        }

        return parent::castAttribute($key, $value);
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
