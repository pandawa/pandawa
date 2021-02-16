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
        return parent::setAttribute(snake_case($key), $value);
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
}
