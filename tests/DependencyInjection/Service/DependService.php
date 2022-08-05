<?php

declare(strict_types=1);

namespace Test\DependencyInjection\Service;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class DependService
{
    public function __construct(protected SingleService $service)
    {
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
