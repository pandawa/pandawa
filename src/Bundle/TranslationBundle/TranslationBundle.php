<?php

declare(strict_types=1);

namespace Pandawa\Bundle\TranslationBundle;

use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Translation\Translator;
use Pandawa\Bundle\FoundationBundle\Plugin\RegisterBundlesPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Component\Foundation\ResourcePublisher;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class TranslationBundle extends Bundle implements HasPluginInterface
{
    public function register(): void
    {
        $this->app->instance('path.lang', $this->app->basePath('resources/trans'));
    }

    public function configure(): void
    {
        ResourcePublisher::publishes(
            static::class,
            $this->getPaths(['auth.php', 'pagination.php', 'passwords.php', 'validation.php']),
            ['default-lang', 'lang']
        );
    }

    public function plugins(): array
    {
        return [
            new RegisterBundlesPlugin([TranslationServiceProvider::class]),
        ];
    }

    protected function translator(): Translator
    {
        return $this->app['translator'];
    }

    protected function getPaths(array $files): array
    {
        $paths = [];

        foreach ($files as $file) {
            $paths[$this->getBundleLangPath($file)] = $this->getAppLangPath($file);
        }

        return $paths;
    }

    protected function getBundleLangPath(string $file): string
    {
        return $this->getPath('Resources/trans/en/' . $file);
    }

    protected function getAppLangPath(string $file): string
    {
        return $this->app['path.lang'] . '/en/' . $file;
    }
}
