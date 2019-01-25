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

use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait TransformerProviderTrait
{
    protected $transformerPath = 'Transformer';

    public function registerTransformerProvider(): void
    {
        if (file_exists($this->app->getCachedConfigPath())) {
            return;
        }

        $basePath = $this->getCurrentPath() . '/' . trim($this->transformerPath, '/');

        if (is_dir($basePath)) {
            $finder = new Finder();

            /** @var SplFileInfo $file */
            foreach ($finder->in($basePath)->files() as $file) {
                $transformerClass = $this->getClassFromFile($file);

                $this->mergeConfig('pandawa_transformers', [
                    md5($transformerClass) => $transformerClass,
                ]);
            }
        }
    }
}
