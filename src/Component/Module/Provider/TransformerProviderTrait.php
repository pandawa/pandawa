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

use Pandawa\Component\Transformer\TransformerInterface;
use Pandawa\Component\Transformer\TransformerRegistryInterface;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait TransformerProviderTrait
{
    protected $transformerPath = 'Transformer';

    public function bootTransformerProvider(): void
    {
        if (null === $this->transformerRegistry()) {
            return;
        }

        $basePath = $this->getCurrentPath() . '/' . trim($this->transformerPath, '/');

        if (is_dir($basePath)) {
            $finder = new Finder();

            /** @var SplFileInfo $file */
            foreach ($finder->in($basePath) as $file) {
                $transformerClass = $this->getClassFromFile($file);

                $this->transformerRegistry()->add($this->createTransformer($transformerClass));
            }
        }
    }

    private function createTransformer(string $class): TransformerInterface
    {
        if (isset($this->app[$class])) {
            return $this->app[$class];
        }

        return $this->app->make($class);
    }

    private function transformerRegistry(): ?TransformerRegistryInterface
    {
        if (isset($this->app[TransformerRegistryInterface::class])) {
            return $this->app[TransformerRegistryInterface::class];
        }

        return null;
    }
}
