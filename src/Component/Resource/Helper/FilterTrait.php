<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Helper;

use Illuminate\Http\Request;
use Pandawa\Contracts\Resource\Model\HandlerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait FilterTrait
{
    protected function applyFilter(HandlerInterface $handler, Request $request): static
    {
        if (empty($filters = $this->getRouteOption('filters', $request))) {
            return $this;
        }

        $newFilters = [];

        foreach ($filters as $criterion) {
            if (is_array($criterion)) {
                $key = array_get($criterion, 'target');
                $criterion = array_get($criterion, 'source');
            } else {
                $key = $criterion;
            }

            $newFilters[$criterion] = $request->route()->parameter($key, $request->get($key));
        }

        $handler->withFilter($newFilters);

        return $this;
    }
}
