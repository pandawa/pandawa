<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\Bootstrap;

use Exception;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadConfiguration as LaravelLoadConfiguration;
use Pandawa\Contracts\Config\LoaderInterface;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class LoadConfiguration extends LaravelLoadConfiguration
{
    public function __construct(protected LoaderInterface $loader)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function loadConfigurationFiles(Application $app, RepositoryContract $repository): void
    {
        $files = $this->getConfigurationFiles($app);

        if (!isset($files['app'])) {
            throw new Exception('Unable to load the "app" configuration file.');
        }

        foreach ($files as $key => $path) {
            $repository->set($key, $this->loader->load($path));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfigurationFiles(Application $app): array
    {
        $files = [];

        $configPath = realpath($app->configPath());

        foreach (Finder::create()->files()->in($configPath) as $file) {
            if (!$this->loader->supports($file->getRealPath())) {
                continue;
            }

            $directory = $this->getNestedDirectory($file, $configPath);
            $extension = pathinfo($file->getRealPath(), PATHINFO_EXTENSION);

            $files[$directory.basename($file->getRealPath(), '.'.$extension)] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }
}
