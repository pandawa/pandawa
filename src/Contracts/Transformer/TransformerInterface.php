<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Transformer;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface TransformerInterface
{
    public function process(Context $context, mixed $data): mixed;
}
