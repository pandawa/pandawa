<?php

declare(strict_types=1);

namespace Pandawa\Bundle\FoundationBundle\Plugin;

use Pandawa\Component\Foundation\Bundle\Plugin;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RegisterBundlesPlugin extends Plugin
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
}
