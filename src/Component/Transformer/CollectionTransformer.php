<?php

declare(strict_types=1);

namespace Pandawa\Component\Transformer;

use Illuminate\Support\Collection;
use Pandawa\Contracts\Transformer\Context;
use Pandawa\Contracts\Transformer\TransformerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class CollectionTransformer implements TransformerInterface
{
    public function __construct(protected readonly TransformerInterface $transformer)
    {
    }

    public function process(Context $context, mixed $data): array
    {
        return Collection::wrap($data)
            ->map(fn(mixed $item) => $this->transformer->process($context, $item))
            ->toArray();
    }
}
