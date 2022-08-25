<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Foundation;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface HasPluginInterface
{
    /**
     * Get the plugins provided by the bundle.
     *
     * @return PluginInterface[]
     */
    public function plugins(): array;
}
