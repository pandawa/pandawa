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
use Illuminate\Foundation\Application;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @property Application $app
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ConfigProviderTrait
{
    /**
     * @var string
     */
    protected $configPath = 'Resources/configs';

    protected function bootConfigProvider(): void
    {
        if (file_exists($this->app->getCachedConfigPath())) {
            return;
        }

        $basePath = $this->getCurrentPath() . '/' . trim($this->configPath, '/');

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
        if (file_exists($this->app->getCachedConfigPath())) {
            return;
        }

        foreach ($this->getConfigFiles() as $file) {
            $this->mergeConfigFrom(
                (string) $file,
                sprintf('modules.%s', pathinfo($file->getBasename(), PATHINFO_FILENAME))
            );
        }
    }

    private function getConfigFiles(): Generator
    {
        $basePath = $this->getCurrentPath() . '/Resources/configs';

        if (is_dir($basePath)) {
            $finder = new Finder();

            /** @var SplFileInfo $file */
            foreach ($finder->in($basePath) as $file) {
                yield $file;
            }
        }
    }
}
