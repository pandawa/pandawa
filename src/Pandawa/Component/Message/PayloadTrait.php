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

namespace Pandawa\Component\Message;

use Borobudur\Component\Parameter\ImmutableParameter;
use Illuminate\Support\Str;
use ReflectionProperty;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait PayloadTrait
{
    /**
     * Filtered payload based on class properties.
     *
     * @var ImmutableParameter
     */
    protected $payload;

    /**
     * The origin payload data.
     *
     * @var ImmutableParameter
     */
    protected $origin;

    public function payload(): ImmutableParameter
    {
        return $this->payload;
    }

    public function origin(): ImmutableParameter
    {
        return $this->origin;
    }

    protected function setPayload(array $payload): void
    {
        $this->origin = new ImmutableParameter($payload);
        $filtered = [];

        foreach ($payload as $key => $value) {
            $property = Str::camel($key);
            if (property_exists($this, $property)) {
                $this->setPropValue($property, $value);
                $filtered[$key] = $value;
            }
        }

        $this->payload = new ImmutableParameter($filtered);
    }

    /**
     * Use this method to initialize message with defaults or extend your class
     *
     * @param array $payload
     */
    abstract protected function init(array &$payload): void;

    /**
     * @param $key
     * @param $value
     *
     * @throws \ReflectionException
     */
    private function setPropValue($key, $value): void
    {
        $property = new ReflectionProperty(get_class($this), $key);
        $property->setAccessible(true);
        $property->setValue($this, $value);
    }
}
