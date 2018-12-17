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

use Illuminate\Contracts\Support\Arrayable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Signature implements Arrayable
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var array
     */
    private $attributes;

    public function __construct(string $token, array $attributes = [])
    {
        $this->token = $token;
        $this->attributes = $attributes;
    }

    public function getCredentials(): string
    {
        return $this->token;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_merge($this->getAttributes(), ['signature' => $this->getCredentials()]);
    }

    public function __toString(): string
    {
        return http_build_query($this->toArray());
    }
}
