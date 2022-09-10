<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Helper;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Pandawa\Contracts\Resource\Model\HandlerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait CriteriaTrait
{
    protected function applyCriteria(HandlerInterface $handler, Request $request): static
    {
        if ($criteria = $this->getCriteria($request)) {
            $handler->withCriteria($criteria);
        }

        return $this;
    }

    protected function getCriteria(Request $request): ?array
    {
        if (empty($criteria = $this->getRouteOption('criteria', $request))) {
            return null;
        }

        return array_map(
            function (array $criterion) use ($request) {
                return $this->container->make(
                    $criterion['class'],
                    !empty($arguments = $criterion['arguments'] ?? [])
                        ? $this->onlyData(
                            $request,
                            array_map(
                                fn(string $key) => Str::camel($key),
                                $arguments
                            )
                          )
                        : []
                );
            },
            $criteria
        );
    }

    private function onlyData(Request $request, array $keys): array
    {
        return Arr::only(
            [
                ...$request->all(),
                ...$request->route()->parameters()
            ],
            $keys
        );
    }
}
