<?php

declare(strict_types=1);

namespace Pandawa\Component\Config\Loader;

use Pandawa\Contracts\Config\LoaderInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class YamlLoader implements LoaderInterface
{
    public function load(string $file): array
    {
        return Yaml::parseFile($file);
    }

    public function supports(string $extension): bool
    {
        return in_array($extension, ['yaml', 'yml'], true);
    }
}
