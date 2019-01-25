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
use InvalidArgumentException;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ServiceProviderTrait
{
    /**
     * @var string
     */
    protected $servicePath = 'Resources/services';

    protected function registerServiceProvider(): void
    {
        foreach ($this->getServiceFiles() as $file) {
            foreach ($this->loader->load((string) $file) as $name => $service) {
                $this->loadService($name, $service);
            }
        }
    }

    protected function loadService(string $name, array $service): void
    {
        $this->app->bind($name, $this->parseService($service), array_get($service, 'shared', true));

        if (null !== $alias = array_get($service, 'alias')) {
            $this->app->alias($name, $alias);
        }

        if (null !== $tag = array_get($service, 'tag')) {
            $this->app->tag([$name], $tag);
        }
    }

    private function parseService(array $service)
    {
        $arguments = array_get($service, 'arguments', []);

        if (null !== $factory = array_get($service, 'factory')) {
            return function () use ($service, $factory, $arguments) {
                $arguments = $this->parseConfigs($arguments);

                return call_user_func_array($this->parseConfigValue($factory), $arguments);
            };
        }

        if (null !== $class = array_get($service, 'class')) {
            if (empty($arguments)) {
                return $this->parseConfigValue($class);
            }

            return function () use ($class, $arguments) {
                $reflection = new ReflectionClass($this->parseConfigValue($class));
                $arguments = $this->parseConfigs($arguments);

                return $reflection->newInstanceArgs($arguments);
            };
        }

        throw new InvalidArgumentException('Service configuration should has factory or class parameter.');
    }

    private function parseConfigs(array $configs): array
    {
        $parsed = [];

        foreach ($configs as $key => $value) {
            if (is_array($value)) {
                $value = $this->parseConfigs($value);
            } else if (is_string($value)) {
                $value = $this->parseConfigValue($value);
            }

            if (is_string($key)) {
                $key = $this->parseConfigValue($key);
            }

            $parsed[$key] = $value;
        }

        return $parsed;
    }

    private function parseConfigValue(string $value)
    {
        if (0 === $index = strpos($value, '@')) {
            return $this->app[substr($value, 1)];
        }

        if (0 === $index = strpos($value, '#')) {
            return $this->app->tagged(substr($value, 1));
        }

        if (preg_match('/^\%([a-zA-Z0-9\.\-\_]+)\%$/', $value, $matches)) {
            return config($matches[1]);
        }

        return $value;
    }

    private function getServiceFiles(): Generator
    {
        $basePath = $this->getCurrentPath() . '/' . trim($this->servicePath, '/');

        if (is_dir($basePath)) {
            /** @var SplFileInfo $file */
            foreach (Finder::create()->in($basePath)->files() as $file) {
                yield $file;
            }
        }
    }
}
