<?php

declare(strict_types=1);

namespace Pandawa\Bundle\TranslationBundle\Plugin;

use Pandawa\Component\Foundation\Bundle\Plugin;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportTranslationPlugin extends Plugin
{
    public function __construct(protected readonly string $translationPath = 'Resources/trans')
    {
    }

    public function configure(): void
    {
        tap($this->bundle->getPath($this->translationPath), function (string $path) {
            $this->loadTranslationsFrom($path, $this->bundle->getName());
            $this->loadJsonTranslationsFrom($path);
        });
    }

    protected function loadTranslationsFrom(string $path, string $namespace): void
    {
        $this->bundle->callAfterResolving('translator', function ($translator) use ($path, $namespace) {
            $translator->addNamespace($namespace, $path);
        });
    }

    protected function loadJsonTranslationsFrom(string $path): void
    {
        $this->bundle->callAfterResolving('translator', function ($translator) use ($path) {
            $translator->addJsonPath($path);
        });
    }
}
