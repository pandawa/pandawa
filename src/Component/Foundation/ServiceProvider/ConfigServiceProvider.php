<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\ServiceProvider;

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
use Pandawa\Component\Config\Parser\ServiceParser;
use Pandawa\Component\Config\Parser\TagParser;
use Pandawa\Contracts\Config\LoaderInterface;
use Pandawa\Contracts\Config\Parser\ParserResolverInterface;
use Pandawa\Contracts\Config\ProcessorInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoaderInterface::class, fn(Container $container) => new ChainLoader([
            $container->make(PhpLoader::class),
            $container->make(YamlLoader::class),
        ]));
        $this->app->singleton(ProcessorInterface::class, ConfigProcessor::class);

        $this->registerParsers();
    }

    protected function registerParsers(): void
    {
        $this->app->singleton(ParserResolverInterface::class, fn(Container $container) => new ParserResolver([
            $this->app->make(ArrayParser::class),
            $this->app->make(EnvParser::class),
            $this->app->make(ConfigParser::class),
            $this->app->make(ServiceParser::class),
            $this->app->make(TagParser::class),
        ]));
    }
}
