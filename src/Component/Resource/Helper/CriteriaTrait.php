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

    protected function getCriteria(Request $request): array
    {
        if (empty($criteria = $this->getRouteOption('criteria', $request))) {
            return [];
        }

        return array_map(
            function (array $criterion) use ($request) {
                return $this->container->make(
                    $criterion['class'],
                    $this->getArguments($request, $criterion)
                );
            },
            $criteria
        );
    }

    private function getArguments(Request $request, array $criteria): array
    {
        return [
            ...$this->mapKeysToCamel($criteria['defaults'] ?? []),
            ...$this->onlyData(
                $request,
                array_map(
                    fn(string $key) => Str::camel($key),
                    $criteria['arguments'] ?? []
                )
            ),
            ...$this->mapKeysToCamel($criteria['values'] ?? []),
        ];
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

    private function mapKeysToCamel(array $data): array
    {
        $keys = [];

        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $key = Str::camel($key);
            }

            $keys[$key] = $value;
        }

        return $keys;
    }
}
