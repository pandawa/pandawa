<?php

declare(strict_types=1);

namespace Pandawa\Bundle\FoundationBundle;

use Illuminate;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Component\Foundation\Bundle\Plugin\RegisterBundlesPlugin;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class FoundationBundle extends Bundle
{
    protected array $registerBundles = [
        Illuminate\Cache\CacheServiceProvider::class,
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
