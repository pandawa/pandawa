<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Helper;

use Illuminate\Http\Request;
use Pandawa\Component\Transformer\Transformer;
use Pandawa\Contracts\Transformer\TransformerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait TransformerTrait
{
    protected function createTransformer(Request $request, TransformerInterface $default): TransformerInterface
    {
        $transformer = $this->getRouteOption('transformer', $request, []);

        if (null !== $class = $transformer['class'] ?? null) {
            $transformer = $this->container->make($class, $transformer['arguments'] ?? []);
        } else {
            $transformer = $default;
        }

        return $this->setUpTransformer($request, $transformer);
    }

    protected function setUpTransformer(Request $request, TransformerInterface $transformer): TransformerInterface
    {
        $context = $this->getRouteOption('transformer.context', $request, []);

        if ($transformer instanceof Transformer) {
            if (!empty($availableIncludes = $context['available_includes'] ?? null)) {
                $transformer->setAvailableIncludes($availableIncludes);
            }

            if (!empty($defaultIncludes = $context['default_includes'] ?? null)) {
                $transformer->setDefaultIncludes($defaultIncludes);
            }

            if (!empty($availableSelects = $context['available_selects'] ?? null)) {
                $transformer->setAvailableSelects($availableSelects);
            }

            if (!empty($defaultSelects = $context['default_selects'] ?? null)) {
                $transformer->setDefaultSelects($defaultSelects);
            }
        }

        return $transformer;
    }
}
