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

namespace Pandawa\Module\Api\Http\Controller;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Pandawa\Component\Ddd\Specification\SpecificationRegistryInterface;
use Pandawa\Component\Message\AbstractCommand;
use Pandawa\Component\Message\AbstractQuery;
use Pandawa\Component\Message\MessageRegistryInterface;
use Pandawa\Component\Message\QueueEnvelope;
use Pandawa\Component\Validation\RequestValidationTrait;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class InvokableController extends Controller implements InvokableControllerInterface
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, InteractsWithRelationsTrait, InteractsWithRendererTrait;
    use RequestValidationTrait;

    public function handle(Request $request)
    {
        $route = $request->route();
        $data = $this->validateRequest($request);

        if (null !== $request->user()) {
            $data['auth_user'] = $request->user()->id;
        }

        $message = $this->getMessage($request);
        $message = new $message($data);

        if ($message instanceof AbstractQuery) {
            $this->modifyQuery($message, $request);
        }

        if ($message instanceof AbstractCommand && $queue = array_get($route->defaults, 'queue')) {
            $message = new QueueEnvelope($message, is_string($queue) ? $queue : null);
        }

        $result = $this->dispatch($message);

        $this->withRelations($result, $route->defaults);

        return $this->render($request, $result, (array) array_get($route->defaults, 'trans', []));
    }

    private function getMessage(Request $request): string
    {
        if (null !== $message = array_get($request->route()->defaults, 'message')) {
            if (null !== $this->messageRegistry()) {
                return $this->messageRegistry()->get($message)->getMessageClass();
            }

            return $message;
        }

        throw new InvalidArgumentException('Parameter "message" not found on route.');
    }

    private function modifyQuery(AbstractQuery $query, Request $request): void
    {
        $route = $request->route();

        if (null !== $withs = array_get($route->defaults, 'withs')) {
            $query->withRelations($withs);
        }

        if (null !== $this->specificationRegistry()) {
            if (null !== $specs = array_get($route->defaults, 'specs')) {
                $specifications = [];
                $data = collect($this->getAllData($request));

                foreach ($specs as $spec) {
                    $arguments = [];

                    if ($specArgs = array_get($spec, 'arguments')) {
                        foreach ($specArgs as $key => $value) {
                            if (is_int($key)) {
                                $arguments[Str::camel($value)] = array_get($data, $value);
                            } else {
                                $arguments[Str::camel($key)] = array_get($data, $key, $value);
                            }
                        }
                    }

                    $specifications[] = $this->specificationRegistry()->get(array_get($spec, 'name'), $arguments);
                }

                $query->withSpecifications($specifications);
            }
        }

        if (true === array_get($route->defaults, 'paginate', false)) {
            $query->paginate((int) $request->get('limit', 50));
        }
    }

    private function messageRegistry(): ?MessageRegistryInterface
    {
        if (app()->has(MessageRegistryInterface::class)) {
            return app(MessageRegistryInterface::class);
        }

        return null;
    }

    private function specificationRegistry(): ?SpecificationRegistryInterface
    {
        if (app()->has(SpecificationRegistryInterface::class)) {
            return app(SpecificationRegistryInterface::class);
        }

        return null;
    }
}
