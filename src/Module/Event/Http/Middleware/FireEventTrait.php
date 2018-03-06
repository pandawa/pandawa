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

namespace Pandawa\Module\Event\Http\Middleware;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Pandawa\Component\Event\EventRegistryInterface;
use ReflectionClass;
use ReflectionException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait FireEventTrait
{
    /**
     * @param Request $request
     * @param string  $eventName
     * @param array   $arguments
     *
     * @throws ReflectionException
     */
    public function fire(Request $request, string $eventName, array $arguments): void
    {
        if ($this->registry()->has($eventName)) {
            $eventName = $this->registry()->get($eventName);
        }

        if (!class_exists($eventName)) {
            throw new InvalidArgumentException(sprintf('Event class "%s" not exist.', $eventName));
        }

        $args = [];
        if (!empty($arguments)) {
            $data = array_merge($request->all(), $request->route()->parameters());
            $args = array_only($data,$arguments);
        }

        $reflection = new ReflectionClass($eventName);
        $reflection->newInstanceArgs($args);
    }

    abstract protected function registry(): EventRegistryInterface;

    abstract protected function dispatcher(): Dispatcher;
}
