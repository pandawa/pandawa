<?php

declare(strict_types=1);

namespace Pandawa\Bundle\FoundationBundle\Plugin;

use Illuminate\Contracts\Support\DeferrableProvider;
use Pandawa\Component\Foundation\Bundle\Plugin;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RegisterBundlesPlugin extends Plugin implements DeferrableProvider
{
    public function __construct(protected array $registerBundles)
    {
    }

    public function configure(): void
    {
        foreach ($this->registerBundles as $bundle) {
            $this->bundle->getApp()->register($bundle);
        }
    }

    public function provides(): array
    {
        $provides = [];

        foreach ($this->registerBundles as $bundle) {
            if ($bundle instanceof DeferrableProvider) {
                $provides = [...$provides, ...$bundle->provides()];
            }
        }

        return $provides;
    }
}
