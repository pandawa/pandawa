<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Helper;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Pandawa\Contracts\Resource\Model\HandlerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait RepositoryTrait
{
    protected function callRepository(array $repository, HandlerInterface $handler, Request $request): mixed
    {
        return $this->container->call(
            [$handler->getRepository(), $repository['call']],
            !empty($arguments = $repository['arguments'] ?? [])
                ? $request->only(array_map(fn(string $key) => Str::camel($key), $arguments))
                : []
        );
    }
}
