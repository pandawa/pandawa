<?php

declare(strict_types=1);

namespace Pandawa\Component\Config\Loader;

use Pandawa\Contracts\Config\LoaderInterface;
use Pandawa\Contracts\Config\Parser\ParserResolverInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class YamlLoader implements LoaderInterface
{
    public function __construct(protected ParserResolverInterface $resolver)
    {
    }

    public function load(string $file): array
    {
        $config = Yaml::parseFile($file);

        if ($parser = $this->resolver->resolve($config)) {
            $config = $parser->parse($config);
        }

        return $config;
    }

    public function supports(string $extension): bool
    {
        return in_array($extension, ['yaml', 'yml'], true);
    }
}
