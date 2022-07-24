<?php

declare(strict_types=1);

namespace Pandawa\Component\DependencyInjection\Factory;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use Pandawa\Contracts\Config\Parser\ParserResolverInterface;
use Pandawa\Contracts\DependencyInjection\Factory\ServiceFactoryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class FactoryServiceFactory implements ServiceFactoryInterface
{
    public function __construct(protected Container $container, protected ParserResolverInterface $resolver)
    {
    }

    public function create(array $config): callable
    {
        $this->validate($config);

        return function () use ($config) {
            $config = $this->resolver->resolve($config)?->parse($config) ?? $config;
            $factory = $config['factory'];
            $arguments = $config['arguments'] ?? [];

            return call_user_func($factory, ...$arguments);
        };
    }

    public function supports(array $config): bool
    {
        return array_key_exists('factory', $config);
    }

    protected function validate(array $config): void
    {
        if (!is_array($config['factory']) && !is_callable($config['factory'])) {
            throw new InvalidArgumentException('Param "factory" should be array or callable.');
        }
    }
}
