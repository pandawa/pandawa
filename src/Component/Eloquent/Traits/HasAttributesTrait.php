<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Traits;

use Pandawa\Component\Eloquent\Model;

/**
 * @mixin Model
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait HasAttributesTrait
{
    /**
     * Force convert key to snake case when set attribute.
     *
     * @param  string  $key
     * @param  mixed  $value
     *
     * @return static|null
     */
    public function setAttribute($key, $value): ?static
    {
        return parent::setAttribute(snake_case($key), $value);
    }

    /**
     * Force convert key to snake case when get attribute.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    public function getAttribute($key): mixed
    {
        return parent::getAttribute(snake_case($key));
    }

    /**
     * Force convert key to camel case when get from relation method.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    public function getRelationshipFromMethod($key): mixed
    {
        return parent::getRelationshipFromMethod(camel_case($key));
    }

    /**
     * Force convert key to camel case when relation is method.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isRelation($key): bool
    {
        if ($this->hasAttributeMutator($key)) {
            return false;
        }

        $key = camel_case($key);

        return method_exists($this, $key) ||
            (static::$relationResolvers[get_class($this)][$key] ?? null);
    }
}
