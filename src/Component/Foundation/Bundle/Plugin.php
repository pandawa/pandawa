<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\Bundle;

use Pandawa\Contracts\Foundation\BundleInterface;
use Pandawa\Contracts\Foundation\PluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class Plugin implements PluginInterface
{
    protected ?BundleInterface $bundle = null;

    public function setBundle(BundleInterface $bundle): void
    {
        $this->bundle = $bundle;
    }

    public function configure(): void
    {
    }

    public function boot(): void
    {
    }
}
