<?php

declare(strict_types=1);

namespace Test\DependencyInjection\Service;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class SingleService
{
    public function __construct(protected bool $debug)
    {
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }
}
