<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Resource;

use Pandawa\Contracts\Transformer\Context;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Rendering
{
    public function __construct(
        public readonly array $data,
        public readonly Context $context
    ) {
    }

    public function merge(array $data): self
    {
        return new self([...$this->data, ...$data], $this->context);
    }

    public function remove(string $key): self
    {
        $data = $this->data;
        unset($data[$key]);

        return new self($data, $this->context);
    }
}
