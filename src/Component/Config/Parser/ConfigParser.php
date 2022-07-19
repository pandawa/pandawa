<?php

declare(strict_types=1);

namespace Pandawa\Component\Config\Parser;

use Illuminate\Contracts\Config\Repository;
use Pandawa\Component\Config\Helpers\Replacer;
use Pandawa\Component\Config\Traits\ParserResolverTrait;
use Pandawa\Contracts\Config\Parser\ParserInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ConfigParser implements ParserInterface
{
    use ParserResolverTrait;

    const REGEX = '/\%([a-zA-Z0-9\.\-\_]+?)\%/';

    public function __construct(protected Repository $config)
    {
    }

    public function parse(mixed $value): mixed
    {
        preg_match_all(self::REGEX, $value, $matches);

        $value = Replacer::replace($matches, $value, fn($key) => $this->config->get($key));

        if (is_string($value) && $parser = $this->resolver->resolve($value)) {
            $value = $parser->parse($value);
        }

        return $value;
    }

    public function supports(mixed $value): bool
    {
        return is_string($value) && preg_match(self::REGEX, $value);
    }

    protected function normalizeValue(mixed $value): string
    {
        if (true === $value) {
            return 'true';
        }

        if (false === $value) {
            return 'false';
        }

        return (string)$value;
    }
}
