<?php

declare(strict_types=1);

namespace Test\DependencyInjection\Service;

use Pandawa\Annotations\DependencyInjection\Inject;
use Pandawa\Annotations\DependencyInjection\Injectable;
use Pandawa\Annotations\DependencyInjection\Type;
use Test\DependencyInjection\Contracts\ServiceInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Injectable]
class ChildService implements ServiceInterface
{
    public function __construct(
        #[Inject(Type::VALUE, 'dummy')]
        private readonly string $name
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
