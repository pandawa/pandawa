<?php

declare(strict_types=1);

namespace Test\DependencyInjection\Service;

use Test\DependencyInjection\Contracts\ServiceInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ServiceManager
{
    private array $services = [];

    public function __construct(iterable $services = [])
    {
        foreach ($services as $service) {
            $this->addService($service);
        }
    }

    public function hasService(string $name): bool
    {
        return array_key_exists($name, $this->services);
    }

    public function addService(ServiceInterface $service): void
    {
        $this->services[$service->getName()] = $service;
    }
}
