<?php

declare(strict_types=1);

namespace Pandawa\Component\Transformer;

use Illuminate\Contracts\Support\Arrayable;
use Pandawa\Contracts\Transformer\Context;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class DataTransformer extends Transformer
{
    protected function transform(Context $context, mixed $data): mixed
    {
        if ($data instanceof Arrayable) {
            return $data->toArray();
        }

        return $data;
    }
}
