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

namespace Pandawa\Module\Api\Routing;

use Illuminate\Support\Collection;
use Pandawa\Component\Loader\ChainLoader;
use Pandawa\Module\Api\Routing\Loader\LoaderAwareInterface;
use Pandawa\Module\Api\Routing\Loader\LoaderTypeInterface;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class RouteLoader implements RouteLoaderInterface
{
    /**
     * @var LoaderTypeInterface[]|Collection
     */
    private $loaders;

    /**
     * @var ChainLoader
     */
    private $fileLoader;

    /**
     * @var string
     */
    private $dirname;

    /**
     * Constructor.
     *
     * @param array $loaders
     */
    public function __construct(array $loaders)
    {
        $this->loaders = collect();
        $this->fileLoader = ChainLoader::create();

        foreach ($loaders as $loader) {
            $this->add(array_get($loader, 'loader'), array_get($loader, 'priority', 0));
        }
    }

    /**
     * Add loader.
     *
     * @param LoaderTypeInterface $loader
     * @param int                 $priority
     */
    public function add(LoaderTypeInterface $loader, int $priority = 0): void
    {
        if ($loader instanceof LoaderAwareInterface) {
            $loader->setLoader($this);
        }

        $this->loaders[] = ['priority' => $priority, 'loader' => $loader];
    }

    /**
     * Load route from file.
     *
     * @param string $file
     */
    public function loadFile(string $file): void
    {
        if (!file_exists($file = $this->getFile($file))) {
            throw new RuntimeException(sprintf('File "%s" not found.', $file));
        }

        $routes = $this->fileLoader->load($file);
        if ($routes && is_array($routes)) {
            $this->load($routes);
        }
    }

    /**
     * Load routes.
     *
     * @param array $routes
     */
    public function load(array $routes): void
    {
        foreach ($routes as $route) {
            if (!is_array($route) || empty(array_get($route, 'type'))) {
                throw new RuntimeException('Route config should be array and "type" index should exist and not empty.');
            }

            $loader = $this->getLoader(array_get($route, 'type'));

            if ($loader instanceof LoaderAwareInterface) {
                $loader->setLoader($this);
            }

            $loader->load($route);
        }
    }

    /**
     * Get route loader from type.
     *
     * @param string $type
     *
     * @return LoaderTypeInterface
     */
    private function getLoader(string $type): LoaderTypeInterface
    {
        foreach ($this->loaders->sortByDesc('priority') as $item) {
            /** @var LoaderTypeInterface $loader */
            $loader = array_get($item, 'loader');

            if ($loader->support($type)) {
                return $loader;
            }
        }

        throw new RuntimeException(sprintf('There are load loader support for route type "%s".', $type));
    }

    /**
     * @param string $file
     *
     * @return string
     */
    private function getFile(string $file): string
    {
        $dirname = dirname($file);
        if ($this->dirname !== $dirname && '.' !== $dirname) {
            $this->dirname = $dirname;
        }

        if ('.' === $dirname) {
            return sprintf('%s/%s', $this->dirname, $file);
        }

        return $file;
    }
}
