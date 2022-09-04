<?php

declare(strict_types=1);

namespace Pandawa\Bundle\SerializerBundle\Factory;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class SerializerFactory
{
    public function __construct(protected readonly Container $container)
    {
    }

    public function create(array $normalizers, array $encoders): SerializerInterface
    {
        return new Serializer(
            Arr::map($normalizers, $this->map()),
            Arr::map($encoders, $this->map()),
        );
    }

    protected function map(): callable
    {
        return function (string $service) {
            $service = preg_replace('/^\@/', '', $service);

            return $this->container->make($service);
        };
    }
}
