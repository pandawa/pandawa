<?php

declare(strict_types=1);

namespace Pandawa\Component\Config\Loader;

use Pandawa\Contracts\Config\LoaderInterface;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ChainLoader implements LoaderInterface
{
    /**
     * @var LoaderInterface[]
     */
    private array $loaders = [];

    public function __construct(array $loaders)
    {
        $this->loaders = $loaders;
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
