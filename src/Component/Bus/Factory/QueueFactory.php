<?php

declare(strict_types=1);

namespace Pandawa\Component\Bus\Factory;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\Factory;
use Illuminate\Contracts\Queue\Queue;
use Pandawa\Contracts\Bus\QueueFactoryInterface;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class QueueFactory implements QueueFactoryInterface
{
    public function __construct(private readonly Container $container)
    {
    }

    public function create(?string $connection = null): Queue
    {
        if (!$this->container->has(Factory::class)) {
            throw new RuntimeException('Please install pandawa/queue-bundle to enable queue.');
        }

        return $this->container->get(Factory::class)->connection($connection);
    }

    public function supports(): bool
    {
        return $this->container->has(Factory::class);
    }
}
