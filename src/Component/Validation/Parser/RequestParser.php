<?php

declare(strict_types=1);

namespace Pandawa\Component\Validation\Parser;

use Illuminate\Contracts\Container\Container;
use Pandawa\Contracts\Validation\Parser\ParserInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class RequestParser implements ParserInterface
{
    const REGEX = '/^req\((\w+)\)$/';

    public function __construct(private readonly Container $container)
    {
    }

    public function parse(string $value): mixed
    {
        if (preg_match(self::REGEX, $value, $matches)) {
            return $this->container->get('request')->{trim($matches[1])} ?? null;
        }

        return null;
    }

    public function supports(string $value): bool
    {
        return preg_match(self::REGEX, $value);
    }
}
