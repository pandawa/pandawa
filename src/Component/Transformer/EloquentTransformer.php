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
    /**
     * @var callable
     */
    protected $handler;

    public function setTransformHandler(callable $handler): static
    {
        $this->handler = $handler;

        return $this;
    }

    protected function transform(Context $context, Model $model): array
    {
        if (null !== $handler = $this->handler) {
            return $handler($context, $model, $this);
        }

        return $model->attributesToArray();
    }

    protected function processIncludes(Context $context, array $includes, mixed $data): array
    {
        $included = [];
        foreach ($includes as $include) {
            $included[$include] = $data->{Str::camel($include)};
        }

        return $included;
    }
}
