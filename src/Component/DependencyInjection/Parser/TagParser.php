<?php

declare(strict_types=1);

namespace Pandawa\Component\DependencyInjection\Parser;

use Illuminate\Contracts\Container\Container;
use Pandawa\Component\DependencyInjection\Traits\ParserResolverTrait;
use Pandawa\Contracts\Config\Parser\ParserInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class TagParser implements ParserInterface
{
    use ParserResolverTrait;

    public function __construct(protected Container $container)
    {
    }

    public function parse(mixed $value): array
    {
        return iterator_to_array($this->container->tagged(substr($value, 1))->getIterator());
    }

    public function supports(mixed $value): bool
    {
        return is_string($value) && !empty($value) && str_starts_with($value, '#');
    }
}
