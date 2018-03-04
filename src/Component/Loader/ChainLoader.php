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

namespace Pandawa\Component\Loader;

use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ChainLoader implements LoaderInterface
{
    /**
     * @var LoaderInterface[]
     */
    private $loaders;

    public function __construct(array $loaders)
    {
        $this->loaders = $loaders;
    }

    public static function create(): ChainLoader
    {
        return new static(
            [
                new PhpLoader(),
                new YamlLoader(),
            ]
        );
    }

    public function load(string $file): array
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        foreach ($this->loaders as $loader) {
            if (true === $loader->supports($extension)) {
                return $loader->load($file);
            }
        }

        throw new RuntimeException(sprintf('There is no loader for extension "%s".', $extension));
    }

    public function supports(string $file): bool
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        foreach ($this->loaders as $loader) {
            if (true === $loader->supports($extension)) {
                return true;
            }
        }

        return false;
    }
}
