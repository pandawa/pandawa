<?php

declare(strict_types=1);

namespace Pandawa\Component\Transformer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Pandawa\Contracts\Transformer\Context;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class EloquentTransformer extends Transformer
{
    protected function transform(Context $context, Model $model): array
    {
        return $model->toArray();
    }

    protected function processIncludes(array $includes, mixed $data): array
    {
        $included = [];
        foreach ($includes as $include) {
            $included[$include] = $data->{Str::camel($include)};
        }

        return $included;
    }
}
