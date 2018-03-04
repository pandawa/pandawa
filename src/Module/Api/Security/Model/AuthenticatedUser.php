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

namespace Pandawa\Module\Api\Security\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Support\Arrayable;


/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AuthenticatedUser implements AuthenticatableContract, Arrayable
{
    use Authenticatable;

    /**
     * @var array
     */
    private $attributes;

    public function __construct($identifier, array $attributes = [])
    {
        $this->{$this->getAuthIdentifierName()} = $identifier;
        $this->attributes = $attributes;
    }

    public function getKeyName()
    {
        return 'id';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [$this->getAuthIdentifierName() => $this->getAuthIdentifier()];
    }

    public function __get(string $key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        return null;
    }
}
