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

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;
use Pandawa\Component\Ddd\AbstractModel;
use Pandawa\Component\Ddd\Repository\EntityManagerInterface;
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

    protected function registerResourceProvider(): void
    {
        $this->loadResourcesFiles();

        $key = sprintf('pandawa_resources.%s', $this->getModuleName());

        foreach ($this->app['config']->get($key) ?? [] as $resource) {
            $repositoryClass = $resource['repository_class'];
            $modelClass = $resource['model_class'];

            if (null !== $repositoryClass && class_exists($repositoryClass)) {
                $this->app->singleton(
                    $repositoryClass,
                    function (Application $app) use ($modelClass) {
                        /** @var EntityManagerInterface $entityManager */
                        $entityManager = $app->get(EntityManagerInterface::class);

                        return $entityManager->getRepository($modelClass);
                    }
                );
            }
        }
    }

    private function loadResourcesFiles(): void
    {
        if (file_exists($this->app->getCachedConfigPath())) {
            return;
        }

        $basePath = $this->getCurrentPath() . '/' . $this->modelPathName;
        $key = sprintf('pandawa_resources.%s', $this->getModuleName());

        if (is_dir($basePath)) {
            $finder = new Finder();

            /** @var SplFileInfo $file */
            foreach ($finder->in($basePath)->files() as $file) {
                $modelClass = $this->getClassFromFile($file);

                if (is_subclass_of($modelClass, AbstractModel::class)) {
                    $name = substr($modelClass, (int)strrpos($modelClass, '\\') + 1);
                    $name = Str::snake($name);

                    $resources = $this->app['config']->get($key) ?? [];
                    $resources[$name] = [
                        'model_class'      => $modelClass,
                        'repository_class' => $modelClass::{'getRepositoryClass'}(),
                    ];

                    $this->mergeConfig($key, $resources);
                }
            }
        }
    }
}
