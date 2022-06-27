<?php

declare(strict_types=1);

namespace Pandawa\Component\Config;

use Pandawa\Component\Config\Contract\Loader;
use Symfony\Component\Yaml\Yaml;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class YamlLoader implements Loader
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
