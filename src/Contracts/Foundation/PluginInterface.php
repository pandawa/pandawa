<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Foundation;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface PluginInterface
{
    public function setBundle(BundleInterface $bundle): void;

    public function configure(): void;

    public function boot(): void;
}
