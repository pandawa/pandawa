<?php

declare(strict_types=1);

namespace Test\DependencyInjection\Contracts;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ServiceInterface
{
    public function getName(): string;
}
