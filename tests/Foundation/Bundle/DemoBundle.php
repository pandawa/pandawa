<?php

declare(strict_types=1);

namespace Test\Foundation\Bundle;

use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class DemoBundle extends Bundle implements HasPluginInterface
{
    public function plugins(): array
    {
        return [
            new ImportConfigurationPlugin(),
        ];
    }
}
