<?php

declare(strict_types=1);

namespace Test\DependencyInjection\Service;

use Pandawa\Annotations\DependencyInjection\Inject;
use Pandawa\Annotations\DependencyInjection\Injectable;
use Pandawa\Annotations\DependencyInjection\Type;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Injectable(name: 'service.single')]
class SingleService
{
    public function __construct(
        #[Inject(Type::CONFIG, 'debug')]
        protected bool $debug,
    ) {
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }
}
