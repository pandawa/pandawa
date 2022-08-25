<?php

declare(strict_types=1);

namespace Test\Bus\Handler;

use Test\Bus\Command\CalculateSomething;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class CalculateSomethingHandler
{
    public function __invoke(CalculateSomething $message): string
    {
        return 'calculated';
    }
}
