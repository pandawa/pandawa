<?php

declare(strict_types=1);

namespace Pandawa\Component\Config;

use Pandawa\Component\Config\Contract\Loader;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class PhpLoader implements Loader
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
