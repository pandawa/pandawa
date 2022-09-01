<?php

declare(strict_types=1);

namespace Pandawa\Component\Transformer;

use Illuminate\Support\Enumerable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MergeValue
{
    public readonly array $data;

    public function __construct(iterable $data)
    {
        if ($data instanceof Enumerable) {
            $this->data = $data->all();
        } else {
            $this->data = (array) $data;
        }
    }
}
