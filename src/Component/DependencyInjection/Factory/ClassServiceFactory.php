<?php

declare(strict_types=1);

namespace Pandawa\Component\DependencyInjection\Factory;

use InvalidArgumentException;
use Pandawa\Contracts\Config\Parser\ParserResolverInterface;
use Pandawa\Contracts\DependencyInjection\Factory\ServiceFactoryInterface;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ClassServiceFactory implements ServiceFactoryInterface
{
    public function __construct(protected ParserResolverInterface $resolver)
    {
    }

    public function create(array $config, array $arguments): callable
    {
        $this->validate($config);

        return function () use ($config, $arguments) {
            $reflection = new ReflectionClass($config['class']);
            $arguments = $this->resolver->resolve($arguments)?->parse($arguments) ?? [];

            return $reflection->newInstance(...$arguments);
        };
    }

    protected function validate(array $config): void
    {
        if (!is_string($config['class'])) {
            throw new InvalidArgumentException('Param "class" should be string.');
        }
    }

    public function supports(array $config): bool
    {
        return array_key_exists('class', $config);
    }
}
