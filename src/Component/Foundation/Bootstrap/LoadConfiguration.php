<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\Bootstrap;

use Exception;
use Illuminate\Config\Repository;
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

    public function bootstrap(Application $app)
    {
        $items = [];

        // First we will see if we have a cache configuration file. If we do, we'll load
        // the configuration items from that file so that it is very quick. Otherwise
        // we will need to spin through every configuration file and load them all.
        if (file_exists($cached = $app->getCachedConfigPath())) {
            $items = require $cached;

            $loadedFromCache = true;
        }

        // Next we will spin through all of the configuration files in the configuration
        // directory and load each one into the repository. This will make all of the
        // options available to the developer for use in various parts of this app.
        $app->instance('config', $config = new Repository([...$app['config']->all(), ...$items]));

        if (!isset($loadedFromCache)) {
            $this->loadConfigurationFiles($app, $config);
        }

        // Finally, we will set the application's environment based on the configuration
        // values that were loaded. We will pass a callback which will be used to get
        // the environment in a web context where an "--env" switch is not present.
        $app->detectEnvironment(function () use ($config) {
            return $config->get('app.env', 'production');
        });

        date_default_timezone_set($config->get('app.timezone', 'UTC'));

        mb_internal_encoding('UTF-8');
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
