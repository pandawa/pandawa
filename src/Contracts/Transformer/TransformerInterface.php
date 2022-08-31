<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Transformer;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface TransformerInterface
{
    public function setWrapper(?string $wrapper): void;

    public function getWrapper(): ?string;

    public function process(Context $context, mixed $data): mixed;

    public function wrap(mixed $data): mixed;
}
