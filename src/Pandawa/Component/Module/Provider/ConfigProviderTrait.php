<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Component\Module\Provider;

use Generator;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ConfigProviderTrait
{
    protected function bootConfigProvider(): void
    {
        $basePath = $this->getCurrentPath() . '/Resources/configs';

        if (is_dir($basePath)) {
            $configs = [];

            /** @var SplFileInfo $file */
            foreach ($this->getConfigFiles() as $file) {
                $configs[(string) $file] = config_path('modules/' . $file->getBasename());
            }

            $this->publishes($configs, 'config');
        }
    }

    protected function registerConfigProvider(): void
    {
        foreach ($this->getConfigFiles() as $file) {
            $this->mergeConfigFrom(
                (string) $file,
                sprintf('modules.%s', pathinfo($file->getBasename(), PATHINFO_FILENAME))
            );
        }
    }

    private function getConfigFiles(): Generator
    {
        $basePath = $this->getCurrentPath() . '/Resources/config';

        if (is_dir($basePath)) {
            $finder = new Finder();

            /** @var SplFileInfo $file */
            foreach ($finder->in($basePath)->name('*.php') as $file) {
                yield $file;
            }
        }
    }
}
