<?php

declare(strict_types=1);

namespace Test\DependencyInjection\Service;

use Test\DependencyInjection\Contracts\ServiceInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ChildService implements ServiceInterface
{
    public function __construct(private readonly string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
