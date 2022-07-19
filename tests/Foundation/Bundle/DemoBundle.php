<?php

declare(strict_types=1);

namespace Test\Foundation\Bundle;

use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class DemoBundle extends Bundle
{
    public function plugins(): array
    {
        return [
            new ImportConfigurationPlugin(),
        ];
    }
}
