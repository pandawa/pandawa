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
     * @param string $eventName
     * @param array  $data
     *
     * @throws ReflectionException
     */
    public function fire(string $eventName, array $data): void
    {
        if ($this->registry()->has($eventName)) {
            $eventName = $this->registry()->get($eventName);
        }

        if (!class_exists($eventName)) {
            throw new InvalidArgumentException(sprintf('Event class "%s" not exist.', $eventName));
        }

        $reflection = new ReflectionClass($eventName);

        $this->dispatcher()->dispatch($reflection->newInstance($data));
    }

    /**
     * @param Request $request
     * @param array   $extra
     * @param array   $mappers
     *
     * @return array
     */
    protected function getData(Request $request, array $extra, array $mappers): array
    {
        $data = array_merge($request->all(), $request->route()->parameters(), $extra);

        foreach ($mappers as $mapper) {
            $parts = explode('=', $mapper);

            if (count($parts) != 2) {
                throw new InvalidArgumentException('Mapper format should be "target=source"');
            }

            $data[trim($parts[0])] = array_get($data, trim($parts[1]));
        }

        return $data;
    }

    abstract protected function registry(): EventRegistryInterface;

    abstract protected function dispatcher(): Dispatcher;
}
