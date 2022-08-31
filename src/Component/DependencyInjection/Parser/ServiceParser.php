<?php

declare(strict_types=1);

namespace Pandawa\Component\DependencyInjection\Parser;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use Pandawa\Component\DependencyInjection\Traits\ParserResolverTrait;
use Pandawa\Contracts\Config\Parser\ParserInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ServiceParser implements ParserInterface
{
    use ParserResolverTrait;

    public function __construct(protected Container $container)
    {
    }

    public function parse(mixed $value): mixed
    {
        $service = substr($value, 1);

        if (!$this->container->has($service)) {
            throw new InvalidArgumentException(
                sprintf('Service "%s" is not defined.', $service)
            );
        }

        return $this->container->get($service);
    }

    public function supports(mixed $value): bool
    {
        return is_string($value) && !empty($value) && str_starts_with($value, '@');
    }
}
