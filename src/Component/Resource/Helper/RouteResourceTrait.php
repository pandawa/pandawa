<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Helper;

use Illuminate\Http\Request;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait RouteResourceTrait
{
    protected function getResource(Request $request): string
    {
        return $this->getRouteOption('resource', $request);
    }

    protected function getRouteOption(string $key, Request $request, mixed $default = null): mixed
    {
        return array_get($this->getRouteOptions($request), $key, $default);
    }

    protected function getRouteOptions(Request $request): array
    {
        return $request->route()->defaults;
    }
}
