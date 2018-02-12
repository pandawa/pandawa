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

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as LaravelCollection;
use Pandawa\Component\Serializer\DeserializableInterface;
use Pandawa\Component\Serializer\SerializableInterface;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractModel extends Eloquent
{
    use ModelUuidTrait;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $hidden = ['pivot'];

    public function getId()
    {
        return $this->getKey();
    }

    /**
     * @param array $options
     * @deprecated
     */
    public function save(array $options = []): void
    {
        throw new RuntimeException('Forbidden save directly from model. Please use repository instead.');
    }

    /**
     * @deprecated
     */
    public function delete(): void
    {
        throw new RuntimeException('Forbidden delete directly from model. Please use repository instead.');
    }

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
     * @param array $entities
     *
     * @return Collection
     */
    public function newCollection(array $entities = []): Collection
    {
        return new Collection($entities);
    }

    /**
     * Cast model to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->serialize([]);
    }

    /**
     * Perform persist model.
     *
     * @param array $options
     *
     * @return bool
     */
    protected function persist(array $options = []): bool
    {
        return parent::save($options);
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function remove(): bool
    {
        return parent::delete();
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
     * Perform serialize.
     *
     * @param array $parents
     *
     * @return array|mixed
     */
    private function serialize(array $parents)
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
    private function serializeRelationship(array $parents): array
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
                    function (AbstractModel $model) use (&$temp, $parents, $value) {
                        if ($model instanceof AbstractModel) {
                            $temp[] = $model->serialize(array_merge($parents, [spl_object_hash($value) => true]));

                            return;
                        }

                        $temp[] = $model->toArray();
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
