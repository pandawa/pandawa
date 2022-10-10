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
     * Force convert key to camel case when get relation value.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    public function getRelationValue($key): mixed
    {
        return parent::getRelationValue(camel_case($key));
    }
}
