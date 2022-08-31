<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Helper;

use Illuminate\Http\Request;
use Pandawa\Contracts\Transformer\Context;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ContextualTrait
{
    protected function createContext(Request $request, array $options): Context
    {
        return new Context(
            $this->parse('include', $request),
            $this->parse('select', $request),
            $request->route('version'),
            $options,
            $request
        );
    }

    protected function parse(string $key, Request $request): array
    {
        if (!empty($options = trim($request->query($key, '')))) {
            return array_filter(
                array_map(
                    fn(string $item) => trim($item),
                    explode(',', $options)
                )
            );
        }

        return [];
    }
}
