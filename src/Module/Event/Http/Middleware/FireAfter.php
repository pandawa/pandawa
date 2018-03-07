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

use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Pandawa\Component\Event\EventRegistryInterface;
use ReflectionException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class FireAfter
{
    use FireEventTrait;

    /**
     * @var EventRegistryInterface
     */
    private $registry;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param EventRegistryInterface $registry
     * @param Dispatcher             $dispatcher
     */
    public function __construct(EventRegistryInterface $registry, Dispatcher $dispatcher)
    {
        $this->registry = $registry;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Request  $request
     * @param Closure  $next
     * @param string   $eventName
     * @param string[] ...$mappers
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function handle(Request $request, Closure $next, string $eventName, string ...$mappers)
    {
        /** @var Response $results */
        $results = $next($request);

        if ($results->isSuccessful()) {
            $extra = [];

            if ($results instanceof JsonResponse) {
                $extra['result'] = $results->getData(true);
            }

            $this->fire($eventName, $this->getData($request, $extra, $mappers));
        }

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    protected function registry(): EventRegistryInterface
    {
        return $this->registry;
    }

    /**
     * {@inheritdoc}
     */
    protected function dispatcher(): Dispatcher
    {
        return $this->dispatcher;
    }
}
