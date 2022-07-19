<?php

declare(strict_types=1);

namespace Test\DependencyInjection\Factory;

use Test\DependencyInjection\Service\SingleService;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MyFactory
{
    public function __construct()
    {
    }

    public function create(bool $debug): SingleService
    {
        return new SingleService($debug);
    }
}
