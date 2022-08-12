<?php

declare(strict_types=1);

namespace Pandawa\Component\Annotation\Factory;

use Illuminate\Contracts\Container\Container;
use Pandawa\Contracts\Annotation\Factory\ReaderFactoryInterface;
use Spiral\Attributes\Composite\SelectiveReader;
use Spiral\Attributes\ReaderInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ReaderFactory implements ReaderFactoryInterface
{
    public function __construct(
        protected readonly array $readers,
        protected readonly Container $container,
    ) {
    }

    public function create(): ReaderInterface
    {
        return new SelectiveReader(array_map(
            fn(string $class) => $this->container->make($class),
            $this->readers
        ));
    }
}
