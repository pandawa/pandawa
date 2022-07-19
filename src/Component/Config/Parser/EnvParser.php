<?php

declare(strict_types=1);

namespace Pandawa\Component\Config\Parser;

use Pandawa\Component\Config\Helpers\Replacer;
use Pandawa\Component\Config\Traits\ParserResolverTrait;
use Pandawa\Contracts\Config\Parser\ParserInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class EnvParser implements ParserInterface
{
    use ParserResolverTrait;

    const REGEX = '/env\((.+?)\)/';
    const ARG_REGEX = '/,(?=([^\"]*\"[^\"]*\")*[^\"]*$)/';

    public function parse(mixed $value): mixed
    {
        preg_match_all(self::REGEX, $value, $matches);

        $value = Replacer::replace($matches, $value, function ($key) {
            $args = preg_split(self::ARG_REGEX, $key);

            return env(...$args);
        });

        if (is_string($value) && $parser = $this->resolver->resolve($value)) {
            $value = $parser->parse($value);
        }

        return $value;
    }

    public function supports(mixed $value): bool
    {
        return is_string($value) && preg_match(self::REGEX, $value);
    }
}
