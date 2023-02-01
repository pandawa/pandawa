<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\Http;

use Illuminate\Http\Request;

/**
 * @mixin Request
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class CurrentRequest
{
    public function __call(string $method, array $arguments = []): mixed
    {
        return request()->{$method}(...$arguments);
    }

    public function __get(string $key): mixed
    {
        return request()->{$key};
    }

    public function __set(string $key, mixed $value): void
    {
        request()->{$key} = $value;
    }
}
