<?php

declare(strict_types=1);

namespace Pandawa\Component\Validation\Parser;

use Illuminate\Contracts\Container\Container;
use Pandawa\Contracts\Validation\Parser\ParserInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class AuthParser implements ParserInterface
{
    const REGEX = '/^auth\((\w+)\)$/';

    public function __construct(private readonly Container $container)
    {
    }

    public function parse(string $value): mixed
    {
        if (preg_match(self::REGEX, $value, $matches)) {
            return $this->container->get('request')->user()?->{trim($matches[1])};
        }

        return null;
    }

    public function supports(string $value): bool
    {
        return (bool)preg_match(self::REGEX, $value);
    }
}
