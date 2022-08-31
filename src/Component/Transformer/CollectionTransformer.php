<?php

declare(strict_types=1);

namespace Pandawa\Component\Transformer;

use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Pandawa\Contracts\Transformer\Context;
use Pandawa\Contracts\Transformer\TransformerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class CollectionTransformer implements TransformerInterface
{
    public function __construct(protected readonly ?TransformerInterface $transformer = null)
    {
    }

    public function process(Context $context, mixed $data): array
    {
        return ($data instanceof Enumerable ? $data : Collection::wrap($data))
            ->map($this->map($context))
            ->toArray();
    }

    public function wrap(mixed $data): array
    {
        if (null === $this->getWrapper()) {
            return $data;
        }

        return [
            $this->getWrapper() => $data,
        ];
    }

    public function setWrapper(?string $wrapper): void
    {
        $this->transformer->setWrapper($wrapper);
    }

    public function getWrapper(): ?string
    {
        return $this->transformer->getWrapper();
    }

    protected function map(Context $context): callable
    {
        return function ($item) use ($context) {
            if (null === $this->transformer) {
                return $item;
            }

            return $this->transformer->process($context, $item);
        };
    }
}
