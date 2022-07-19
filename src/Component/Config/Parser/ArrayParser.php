<?php

declare(strict_types=1);

namespace Pandawa\Component\Config\Parser;

use Pandawa\Component\Config\Traits\ParserResolverTrait;
use Pandawa\Contracts\Config\Parser\ParserInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ArrayParser implements ParserInterface
{
    use ParserResolverTrait;

    public function parse(mixed $value): array
    {
        $parsed = [];

        foreach ($value as $key => $config) {
            $parsed[$this->parseValue($key)] = $this->parseValue($config);
        }

        return $parsed;
    }

    protected function parseValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return $this->parse($value);
        }

        if (is_string($value) && $loader = $this->resolver->resolve($value)) {
            return $loader->parse($value);
        }

        return $value;
    }

    public function supports(mixed $value): bool
    {
        return is_array($value);
    }
}
