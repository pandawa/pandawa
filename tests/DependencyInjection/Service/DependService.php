<?php

declare(strict_types=1);

namespace Test\DependencyInjection\Service;

use Pandawa\Annotations\DependencyInjection\Inject;
use Pandawa\Annotations\DependencyInjection\Injectable;
use Pandawa\Annotations\DependencyInjection\Type;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Injectable(name: 'service.depend', alias: 'alias_service')]
class DependService
{
    public function __construct(
        #[Inject(Type::SERVICE, 'service.single')]
        protected SingleService $service,
    ) {
    }

    public function run(): string
    {
        $data = '';

        if ($this->service->isDebug()) {
            $data .= 'DEBUG-';
        }

        $data .= 'PING';

        return $data;
    }
}
