<?php

declare(strict_types=1);

namespace Pandawa\Component\Config\Loader;

use Pandawa\Contracts\Config\LoaderInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class PhpLoader implements LoaderInterface
{
    public function load(string $file): array
    {
        return require $file;
    }

    public function supports(string $extension): bool
    {
        return 'php' === $extension;
    }
}
