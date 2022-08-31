<?php

declare(strict_types=1);

namespace Pandawa\Component\Transformer;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ConditionallyTrait
{
    public function when(bool $condition, mixed $value, mixed $default = null): mixed
    {
        if ($condition) {
            return value($value);
        }

        return 3 === func_num_args() ? value($default) : new MissingValue();
    }

    public function merge(iterable|callable $value): MergeValue|MissingValue
    {
        return $this->mergeWhen(true, $value);
    }

    public function mergeUnless(bool $condition, iterable|callable $value): MergeValue|MissingValue
    {
        return $this->mergeWhen(!$condition, $value);
    }

    public function mergeWhen(bool $condition, iterable|callable $value): MergeValue|MissingValue
    {
        return $condition ? new MergeValue(value($value)) : new MissingValue();
    }
}
