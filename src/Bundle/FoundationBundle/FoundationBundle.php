<?php

declare(strict_types=1);

namespace Pandawa\Bundle\FoundationBundle;

use Illuminate;
use Pandawa\Bundle\FoundationBundle\Plugin\RegisterBundlesPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class FoundationBundle extends Bundle
{
    protected array $registerBundles = [
        ServiceProvider\ConsoleServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
    ];

    public function plugins(): array
    {
        return [
            new RegisterBundlesPlugin($this->registerBundles),
        ];
    }
}
