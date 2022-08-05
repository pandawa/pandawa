<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\ServiceProvider;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Pandawa\Component\Config\ConfigProcessor;
use Pandawa\Component\Config\Loader\ChainLoader;
use Pandawa\Component\Config\Loader\PhpLoader;
use Pandawa\Component\Config\Loader\YamlLoader;
use Pandawa\Component\Config\Parser\ArrayParser;
use Pandawa\Component\Config\Parser\ConfigParser;
use Pandawa\Component\Config\Parser\EnvParser;
use Pandawa\Component\Config\Parser\ParserResolver;
use Pandawa\Contracts\Config\LoaderInterface;
use Pandawa\Contracts\Config\ProcessorInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register temporary config that used by container.
        $this->app->instance('config', new Repository());

        $this->registerParsers();
        $this->registerLoaders();
        $this->registerProcessor();
    }

    protected function registerParsers(): void
    {
        $this->app->bind('config.parser.array', ArrayParser::class);
        $this->app->bind('config.parser.env', EnvParser::class);
        $this->app->bind('config.parser.config', ConfigParser::class);

        $this->app->tag(['config.parser.array', 'config.parser.env', 'config.parser.config'], 'ConfigParsers');

        $this->app->bind('config.resolver.parser', fn(Container $container) => new ParserResolver(
            iterator_to_array($container->tagged('ConfigParsers')->getIterator()),
        ));
    }

    protected function registerLoaders(): void
    {
        $this->app->bind('config.loader.php', PhpLoader::class);
        $this->app->bind('config.loader.yaml', fn(Container $container) => new YamlLoader(
            $container->get('config.resolver.parser'),
        ));

        $this->app->tag(['config.loader.php', 'config.loader.yaml'], 'ConfigLoaders');

        $this->app->bind('loader', fn(Container $container) => new ChainLoader(
            iterator_to_array($this->app->tagged('ConfigLoaders')->getIterator()),
        ));
        $this->app->alias('loader', LoaderInterface::class);
    }

    protected function registerProcessor(): void
    {
        $this->app->singleton('config.processor', ConfigProcessor::class);
        $this->app->alias('config.processor', ProcessorInterface::class);
    }
}
