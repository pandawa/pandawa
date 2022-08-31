<?php

declare(strict_types=1);

namespace Pandawa\Bundle\ResourceBundle;

use Illuminate\Contracts\Support\DeferrableProvider;
use Pandawa\Bundle\DependencyInjectionBundle\Plugin\ImportServicesPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ResourceBundle extends Bundle implements HasPluginInterface, DeferrableProvider
{
    public function plugins(): array
    {
        return [
            new ImportConfigurationPlugin(),
            new ImportServicesPlugin(),
        ];
    }
}
