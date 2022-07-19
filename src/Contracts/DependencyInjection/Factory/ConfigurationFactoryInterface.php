<?php

declare(strict_types=1);

namespace Pandawa\Contracts\DependencyInjection\Factory;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ConfigurationFactoryInterface
{
    public function create(string $name): TreeBuilder;
}
