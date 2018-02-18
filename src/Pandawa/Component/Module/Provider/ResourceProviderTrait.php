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

use Illuminate\Support\Str;
use Pandawa\Component\Ddd\AbstractModel;
use Pandawa\Component\Resource\Metadata;
use Pandawa\Component\Resource\ResourceRegistryInterface;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ResourceProviderTrait
{
    /**
     * @var string
     */
    protected $modelPathName = 'Model';

    protected function bootResourceProvider(): void
    {
        if (null === $this->resourceRegistry()) {
            return;
        }

        $basePath = $this->getCurrentPath() . '/' . $this->modelPathName;

        if (is_dir($basePath)) {
            $finder = new Finder();

            /** @var SplFileInfo $file */
            foreach ($finder->in($basePath) as $file) {
                $modelClass = $this->getClassFromFile($file);

                if (is_subclass_of($modelClass, AbstractModel::class)) {
                    $name = substr($modelClass, (int) strrpos($modelClass, '\\') + 1);
                    $name = Str::snake($name);

                    $this->resourceRegistry()->add($name, new Metadata($modelClass));
                }
            }
        }
    }

    private function resourceRegistry(): ?ResourceRegistryInterface
    {
        if (isset($this->app[ResourceRegistryInterface::class])) {
            return $this->app[ResourceRegistryInterface::class];
        }

        return null;
    }
}
